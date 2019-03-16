# redis信息整理
  * 事务
  * redis数据开发设计
  * 主从复制
  * 集群分片
  * 数据备份策略
  * 常见reds错误分析
  * 监控redis的服务状态
  * 可视化管理工具
  * [redis防止商品超发](#redis防止商品超发) 
  * redis持久化

## redis防止商品超发
#### 公用方法 function.php
```
<?php
//获取商品key名称
function getKeyName($v)
{
    return "send_goods_".$v;
}

//日志写入方法
function writeLog($msg,$v)
{
    $log = $msg.PHP_EOL;
    file_put_contents("log/$v.log",$log,FILE_APPEND);
}
```

#### 自定义redis操作类 myRedis.php
```
<?php
/**
 * @desc 自定义redis操作类
 * **/
class myRedis{
    public $handler = NULL;
    public function __construct($conf){
        $this->handler = new Redis();
        $this->handler->connect($conf['host'], $conf['port']); //连接Redis
        //设置密码
        if(isset($conf['auth'])){
            $this->handler->auth($conf['auth']); //密码验证
        }
        //选择数据库
        if(isset($conf['db'])){
            $this->handler->select($conf['db']);//选择数据库2
        }else{
            $this->handler->select(0);//默认选择0库
        }
    }

    //获取key的值
    public function get($name){
        return $this->handler->get($name);
    }
    
    //设置key的值
    public function set($name,$value){
        return $this->handler->set($name,$value);
    }

    //判断key是否存在
    public function exists($key){
        if($this->handler->exists($key)){
            return true;
        }
        return false;
    }

    //当key不存在的设置key的值，存在则不设置
    public function setnx($key,$value){
        return $this->handler->setnx($key,$value);
    }

    //将key的数值增加指定数值
    public function incrby($key,$value){
        return $this->handler->incrBy($key,$value);
    }
    
}
```

#### 抢购业务实现 index.php
```
<?php
require_once './myRedis.php';
require_once './function.php';

class sendAward{
    public $conf = [];
    const V1 = 'way1';//版本一
    const V2 = 'way2';//版本二
    const AMOUNTLIMIT = 5;//抢购数量限制
    const INCRAMOUNT = 1;//redis递增数量值
    
    //初始化调用对应方法执行商品发放
    public function __construct($conf,$type){
        $this->conf = $conf;
        if(empty($type))
            return '';
        if($type==self::V1){
            $this->way1(self::V1);
        }elseif($type==self::V2){
            $this->way2(self::V2);
        }else{
            return '';
        }
    }
    
    //抢购商品方式一
    protected  function way1($v){
        $redis = new myRedis($this->conf);      
        $keyNmae = getKeyName($v);
        if(!$redis->exists($keyNmae)){
            $redis->set($keyNmae,0);
        }
        $currAmount = $redis->get($keyNmae);
        if(($currAmount+self::INCRAMOUNT)>self::AMOUNTLIMIT){
            writeLog("没有抢到商品",$v);
            return;
        }
        $redis->incrby($keyNmae,self::INCRAMOUNT);
        writeLog("抢到商品",$v);
    }
    
    //抢购商品方式二
    protected function way2($v){
        $redis = new myRedis($this->conf);
        $keyNmae = getKeyName($v);
        if(!$redis->exists($keyNmae)){
            $redis->setnx($keyNmae,0);
        }
        if($redis->incrby($keyNmae,self::INCRAMOUNT) > self::AMOUNTLIMIT){
            writeLog("没有抢到商品",$v);
            return;
        }
        writeLog("抢到商品",$v);
    }
            
}

//实例化调用对应执行方法
$type = isset($_GET['v'])?$_GET['v']:'way1';
$conf = [
    'host'=>'192.168.0.214','port'=>'6379',
    'auth'=>'test','db'=>2,
];
new sendAward($conf,$type);
/***
通过ab工具压力测试模拟超发的情况，再结合日志打印的数据说明方法可以有效的防止超发
ab -c 100 -n 200 http://192.168.0.213:8083/index.php?v=way2
ab -c 100 -n 200 http://192.168.0.213:8083/index.php?v=way2
**/
```

