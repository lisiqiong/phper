php高级知识点 
* [php自动加载](#php自动加载)
* [php反射](#php反射)
* php依赖注入
* PHP中间件
* [php接口数据安全解决方案一](#php接口数据安全解决方案一)
* [php接口数据安全解决方案二](#php接口数据安全解决方案二)
* php7新特性
## php反射
---
```
<?php
/**
 *php反射机制使用场景
 *1.框架底层，比如tp框架底层的控制器调度
 *2.扩展功能
 *3.管理大量的未知类
 * 反射机制-基本使用
 * 1.使用反射机制查看类的结构
 * 2.使用反射机制实现代理调用（  这也是反射最核心的价值）
 */
class IndexAction{

    //方法执行行处理
    public function _before_index($param=''){
        echo __FUNCTION__."执行before_index方法".$param.PHP_EOL;
    }

    public function index(){
        echo "执行index方法".PHP_EOL;
    }

    public function test($year,$month,$day){
        echo $year.'---'.$month.'---'.$day.PHP_EOL;
    }

    //方法执行后处理
    public function _after_index(){
        echo __FUNCTION__.'执行after_index方法'.PHP_EOL;
    }

}

// $test =  new IndexAction();
// echo $test->test(12,2,3);

// //使用反射来代理调用
// //1.获取到类的反射对象
// $index_reflect_class = new ReflectionClass('IndexAction');

// //2.通过反射对象得到实例
// $index1 = $index_reflect_class->newInstance();
// //var_dump($index1);

// //3.获取反射方法的对象实例
// $show_method = $index_reflect_class->getMethod('showInfo');

// //4.通过反射方法调用showinfo
// $show_method->invoke($index1);


/*
 * php thinkphp控制器调度机制
 * 1.indexAction中的方法和访问修饰符是不确定的，如果是public可以执行
 * 2.如果存在_before_index方法，并且是public的，执行该方法
 * 3.再判断有没有_after_index方法，并且是public的，执行该方法
 */
$index_reflect_class = new ReflectionClass('IndexAction');
//通过反射取得实例对象
$controller = $index_reflect_class->newInstance();
//执行index方法
if($index_reflect_class->hasMethod('index')){
    $index_method = $index_reflect_class->getMethod('index');
    if($index_method->isPublic()){

        //先执行before_index，方法存在并public执行
        if($index_reflect_class->hasMethod('_before_index')){
            $before_index = $index_reflect_class->getMethod('_before_index');
            if($before_index->isPublic()){
                // $params = $before_index->getParameters();
                // print_r($params);
                //通过反射方法调用类实例对象
                $before_index->invoke($controller);
            }
        }

        //执行index方法
        $index_method->invoke($controller);
        //后执行after_index,方法存在并public执行
        if($index_reflect_class->hasMethod('_after_index')){
            $after_index = $index_reflect_class->getMethod('_after_index');
            if($after_index->isPublic()){
            //通过反射方法调用类实例对象
                $after_index->invoke($controller);
            }   
        }

    }else{
        echo "index不是公用方法不不执行".PHP_EOL;
    }
}else{
    echo "index 方法不存在不存在".PHP_EOL;
}

```

运行结果
```
_before_index执行before_index方法
执行index方法
_after_index执行after_index方法
```

## php自动加载
---
#### 下面显示例子的文件目录结构图
![avatar](./images/auto-load.png)
## 一、没有使用命名空间的几种实现
#### test/oneClass.php
```
class oneClass{

    public function show(){
        echo "这里是oneClass.php的show方法<br/>";
    }

}
```

#### test/twoClass.php
```
<?php

class twoClass{

    public function show(){
        echo "这里是twoClass.php的show方法<br/>";
    }

}
```

下面7种方式都可以实现自动加载，结果都为:
```
这里是oneClass.php的show方法
这里是twoClass.php的show方法
```

### 方法一：index.php 使用__autoload()魔术方法实现自动加载

```
<?php
//7.2以后使用这个提示一个警告,Deprecated: __autoload() is deprecated, use spl_autoload_register() instead
function __autoload($classname){
    include './test/'.$classname.'.php';
}

//调用类库如果找不到会自动执行__autoload()
$one = new oneClass();
$one->show();
$two = new twoClass();
$two->show();
```


#### 运行结果
```
Deprecated: __autoload() is deprecated, use spl_autoload_register() instead in /Users/lidong/Desktop/wwwroot/test/April/autoload1/index.php on line 5
这里是oneClass.php的show方法
这里是twoClass.php的show方法
```
#### 总结：在PHP7.2以后使用__autoload()会报一个警告，7.2之前这种方式是没提示的.这种方式，是调用一个找不到的类会自动取调用__autoload()方法然后在方法里面执行include引用，实现自动加载。

### 方法二：index2.php 使用spl_autoload_register()方法实现自动加载，创建自定义register方法调用
```
<?php

function register($classname){
    include "./test/{$classname}.php";
}

spl_autoload_register("register");

$one = new oneClass();
$one->show();
$two = new twoClass();
$two->show();
```

### 方法三：index3.php 使用spl_autoload_register()方法，不定义register方法直接使用回调
```
<?php

spl_autoload_register(function($classname){
    include "./test/{$classname}.php";
});

$one = new oneClass();
$one->show();
$two = new twoClass();
$two->show();
```

#### 方法四：index4.php 使用spl_autoload_register()方法，调用类的register方法实现自动加载
```
class autoLoad{
    public static function register($classname){
        include "./test/{$classname}.php";
    } 
}

spl_autoload_register(["autoLoad","register"]);

$one = new oneClass();
$one->show();
$two = new twoClass();
$two->show();
```

## 二、使用命名空间的几种实现
#### test2/oneClass.php
```
<?php

namespace auto\test2;
class oneClass{

    public function show(){
        echo "这里是oneClass.php的show方法<br/>";
    }

}
```

#### test2/twoClass.php
```
<?php
namespace auto\test2;
class twoClass{

    public function show(){
        echo "这里是twoClass.php的show方法<br/>";
    }

}
```

#### 方法五：index5.php，使用spl_autoload_register()，调用加载类的register方法，转化传递过来的命名空间实现自动加载
```
<?php

class autoLoad{
    public static function register($classname){
        $arr = explode('\\', $classname);
        include "./test2/{$arr[2]}.php";
    } 
}

spl_autoload_register(["autoLoad","register"]);

$one = new auto\test2\oneClass();
$one->show();
$two = new auto\test2\twoClass();
$two->show();
```

#### 方法六：index6.php 跟方法五类似，区别是use方法调用类实例化时可以直接使用类名，实现自动加载
```
<?php

use auto\test2\oneClass;
use auto\test2\twoClass;

class autoLoad{
    public static function register($classname){
        $arr = explode('\\', $classname);
        include "./test2/{$arr[2]}.php";
    } 
}

spl_autoload_register(["autoLoad","register"]);

$one = new oneClass();
$one->show();
$two = new twoClass();
$two->show();
```

#### 方法七：index7.php 与方法五和六思路一致，只不过加载类放在外部不是引用在统一文件，要点就是命名空间定义的类，要使用也要先include,实现自动加载
###### autoLoad.php
```
<?php

namespace auto;
class autoLoad{
    public static function register($classname){
        $arr = explode('\\', $classname);
        include "./test2/{$arr[2]}.php";
    } 
}
```
###### index7.php
```
<?php
use auto\test2\oneClass;
use auto\test2\twoClass;

include "./autoLoad.php";

spl_autoload_register(["auto\autoLoad","register"]);

$one = new oneClass();
$one->show();
$two = new twoClass();
$two->show();
```


### 总结：所有的自动加载思想都是调用一个没引用的类库后PHP会自动调用自动执行的一个方法，这个方法有可能是类的方法也有可能是普通方法，但不管怎么样都最终使用include执行文件包含，只不过命名空间需要转化下获取类名。另外值得注意的是，如果是一个php的框架自动加载实现也基本一致，只不过他会根据不同文件夹下面的定义判断后include来实现不同文件夹下文件的引用，来实现整个框架的自动加载。

## php接口数据安全解决方案一
***
- [前言](#前言)
- [目录介绍](#目录介绍)
- [登录鉴权图](#登录鉴权图)
- [接口请求安全性校验整体流程图](#接口请求安全性校验整体流程图)
- [代码展示](#代码展示)
- [演示](#演示)
- [后记](#后记)

## 前言
目的：
* 1.实现前后端代码分离，分布式部署
* 2.利用token替代session实现状态保持，token是有时效性的满足退出登录，token存入redis可以解决不同服务器之间session不同步的问题，满足分布式部署
* 3.利用sign，前端按照约定的方式组合加密生成字符串来校验用户传递的参数跟后端接收的参数是否一直，保障接口数据传递的安全
* 4.利用nonce，timestamp来保障每次请求的生成sign不一致，并将sign与nonce组合存入redis，来防止api接口重放

## 目录介绍
***
```

├── Core
│   ├── Common.php（常用的公用方法）
│   ├── Controller.php (控制器基类)
│   └── RedisService.php (redis操作类)
├── config.php (redis以及是否开启关闭接口校验的配置项)
├── login.php (登录获取token入口)
└── user.php（获取用户信息，执行整个接口校验流程）

```

## 登录鉴权图
***
![](https://img2018.cnblogs.com/blog/595183/201906/595183-20190614182945976-471067830.png)

## 接口请求安全性校验整体流程图
***
![](https://img2018.cnblogs.com/blog/595183/201906/595183-20190614182707043-770306388.png)

## 代码展示
### common.php
```
<?php
namespace Core;
/**
 * @desc 公用方法
 * Class Common
 */
class Common{
    /**
     * @desc 输出json数据
     * @param $data
     */
    public static function outJson($code,$msg,$data=null){
        $outData = [
            'code'=>$code,
            'msg'=>$msg,
        ];
        if(!empty($data)){
            $outData['data'] = $data;
        }
        echo  json_encode($outData);
        die();
    }

    /***
     * @desc 创建token
     * @param $uid
     */
    public static function createToken($uid){
        $time = time();
        $rand = mt_rand(100,999);
        $token = md5($time.$rand.'jwt-token'.$uid);
        return $token;
    }

    /**
     * @desc 获取配置信息
     * @param $type 配置信息的类型，为空获取所有配置信息
     */
    public static function getConfig($type=''){
        $config = include "./config.php";
        if(empty($type)){
            return $config;
        }else{
            if(isset($config[$type])){
                return $config[$type];
            }
            return [];
        }
    }

}

```

### RedisService.php
```
<?php
namespace Core;
/*
 *@desc redis类操作文件
 **/
class RedisService{
    private $redis;
    protected $host;
    protected $port;
    protected $auth;
    protected $dbId=0;
    static private $_instance;
    public $error;

    /*
     *@desc 私有化构造函数防止直接实例化
     **/
    private function __construct($config){
        $this->redis    =    new \Redis();
        $this->port        =    $config['port'] ? $config['port'] : 6379;
        $this->host        =    $config['host'];
        if(isset($config['db_id'])){
            $this->dbId = $config['db_id'];
            $this->redis->connect($this->host, $this->port);
        }
        if(isset($config['auth']))
        {
            $this->redis->auth($config['auth']);
            $this->auth    =    $config['auth'];
        }
        $this->redis->select($this->dbId);
    }

    /**
     *@desc 得到实例化的对象
     ***/
    public static function getInstance($config){
        if(!self::$_instance instanceof self) {
            self::$_instance = new self($config);
        }
        return self::$_instance;

    }

    /**
     *@desc 防止克隆
     **/
    private function __clone(){}

    /*
     *@desc 设置字符串类型的值，以及失效时间
     **/
    public function set($key,$value=0,$timeout=0){
        if(empty($value)){
            $this->error = "设置键值不能够为空哦~";
            return $this->error;
        }
        $res = $this->redis->set($key,$value);
        if($timeout){
            $this->redis->expire($key,$timeout);
        }
        return $res;
    }

    /**
     *@desc 获取字符串类型的值
     **/
    public function get($key){
        return $this->redis->get($key);
    }

}

```

### Controller.php
```
<?php
namespace Core;
use Core\Common;
use Core\RedisService;

/***
 * @desc 控制器基类
 * Class Controller
 * @package Core
 */
class Controller{
    //接口中的token
    public $token;
    public $mid;
    public $redis;
    public $_config;
    public $sign;
    public $nonce;

    /**
     * @desc 初始化处理
     * 1.获取配置文件
     * 2.获取redis对象
     * 3.token校验
     * 4.校验api的合法性check_api为true校验，为false不用校验
     * 5.sign签名验证
     * 6.校验nonce，预防接口重放
     */
    public function __construct()
    {
        //1.获取配置文件
        $this->_config = Common::getConfig();
        //2.获取redis对象
        $redisConfig = $this->_config['redis'];
        $this->redis = RedisService::getInstance($redisConfig);

        //3.token校验
        $this->checkToken();
        //4.校验api的合法性check_api为true校验，为false不用校验
        if($this->_config['checkApi']){
            // 5. sign签名验证
            $this->checkSign();

            //6.校验nonce，预防接口重放
            $this->checkNonce();
        }
    }

    /**
     * @desc 校验token的有效性
     */
    private  function checkToken(){
        if(!isset($_POST['token'])){
            Common::outJson('10000','token不能够为空');
        }
        $this->token = $_POST['token'];
        $key = "token:".$this->token;
        $mid = $this->redis->get($key);
        if(!$mid){
            Common::outJson('10001','token已过期或不合法，请先登录系统  ');
        }
        $this->mid = $mid;
    }

    /**
     * @desc 校验签名
     */
    private function checkSign(){
        if(!isset($_GET['sign'])){
            Common::outJson('10002','sign校验码为空');
        }
        $this->sign = $_GET['sign'];
        $postParams = $_POST;
        $params = [];
        foreach($postParams as $k=>$v) {
            $params[] = sprintf("%s%s", $k,$v);
        }
        sort($params);
        $apiSerect = $this->_config['apiSerect'];
        $str = sprintf("%s%s%s", $apiSerect, implode('', $params), $apiSerect);
        if ( md5($str) != $this->sign ) {
            Common::outJson('10004','传递的数据被篡改，请求不合法');
        }
    }

    /**
     * @desc nonce校验预防接口重放
     */
    private function checkNonce(){
        if(!isset($_POST['nonce'])){
            Common::outJson('10003','nonce为空');
        }
        $this->nonce = $_POST['nonce'];
        $nonceKey = sprintf("sign:%s:nonce:%s", $this->sign, $this->nonce);
        $nonV = $this->redis->get($nonceKey);
        if ( !empty($nonV)) {
            Common::outJson('10005','该url已经被调用过，不能够重复使用');
        } else {
            $this->redis->set($nonceKey,$this->nonce,360);
        }
    }

}
```

### config.php
```
<?php
return [
    //redis的配置
    'redis' => [
        'host' => 'localhost',
        'port' => '6379',
        'auth' => '123456',
        'db_id' => 0,//redis的第几个数据库仓库
    ],
    //是否开启接口校验，true开启，false，关闭
    'checkApi'=>true,
    //加密sign的盐值
    'apiSerect'=>'test_jwt'
];
```

### login.php
```
<?php
/**
 * @desc 自动加载类库
 */
spl_autoload_register(function($className){
    $arr = explode('\\',$className);
    include $arr[0].'/'.$arr[1].'.php';
});

use Core\Common;
use Core\RedisService;

if(!isset($_POST['username']) || !isset($_POST['pwd'])  ){
    Common::outJson(-1,'请输入用户名和密码');
}
$username = $_POST['username'];
$pwd = $_POST['pwd'];
if($username!='admin' || $pwd!='123456' ){
    Common::outJson(-1,'用户名或密码错误');
}
//创建token并存入redis，token对应的值为用户的id
$config = Common::getConfig('redis');
$redis = RedisService::getInstance($config);
//假设用户id为2
$uid = 2;
$token = Common::createToken($uid);
$key = "token:".$token;
$redis->set($key,$uid,3600);
$data['token'] = $token;
Common::outJson(0,'登录成功',$data);

```

### user.php
```
<?php
/**
 * @desc 自动加载类库
 */
spl_autoload_register(function($className){
    $arr = explode('\\',$className);
    include $arr[0].'/'.$arr[1].'.php';
});

use Core\Controller;
use Core\Common;
class UserController extends Controller{

    /***
     * @desc 获取用户信息
     */
    public function getUser(){
        $userInfo = [
            "id"=>2,
            "name"=>'巴八灵',
            "age"=>30,
        ];
        if($this->mid==$_POST['mid']){
            Common::outJson(0,'成功获取用户信息',$userInfo);
        }else{
            Common::outJson(-1,'未找到该用户信息');
        }
    }
}
//获取用户信息
$user = new  UserController();
$user->getUser();
```

## 演示用户登录
***
    
**简要描述：** 

- 用户登录接口

**请求URL：** 
- ` http://localhost/login.php `
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|username |是  |string |用户名   |
|pwd |是  |string | 密码    |

 **返回示例**

``` 
{
    "code": 0,
    "msg": "登录成功",
    "data": {
        "token": "86b58ada26a20a323f390dd5a92aec2a"
    }
}

{
    "code": -1,
    "msg": "用户名或密码错误"
}

```    

## 演示获取用户信息
    
**简要描述：** 

- 获取用户信息，校验整个接口安全的流程

**请求URL：** 
- `http://localhost/user.php?sign=f39b0f2dea817dd9dbef9e6a2bf478de `
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|token |是  |string |token   |
|mid |是  |int |用户id    |
|nonce     |是  |string | 防止用户重放字符串 md5加密串   |
|timestamp     |是  |int | 当前时间戳    |

 **返回示例**

``` 
{
    "code": 0,
    "msg": "成功获取用户信息",
    "data": {
        "id": 2,
        "name": "巴八灵",
        "age": 30
    }
}

{
    "code": "10005",
    "msg": "该url已经被调用过，不能够重复使用"
}

{
    "code": "10004",
    "msg": "传递的数据被篡改，请求不合法"
}
{
    "code": -1,
    "msg": "未找到该用户信息"
}
```

## 后记
***
上面完整的实现了整个api的安全过程，包括接口token生成时效性合法性验证，接口数据传输防篡改，接口防重放实现。仅仅靠这还不能够最大限制保证接口的安全。条件满足的情况下可以使用https协议从数据底层来提高安全性，另外本实现过程token是使用redis存储，下一篇文章我们将使用第三方开发的库实现JWT的规范操作，来替代redis的使用。

## php接口数据安全解决方案二
***
#### jwt说明
JWT是什么
JWT是json web token缩写。它将用户信息加密到token里，服务器不保存任何用户信息。服务器通过使用保存的密钥验证token的正确性，只要正确即通过验证。基于token的身份验证可以替代传统的cookie+session身份验证方法。
它定义了一种用于简洁，自包含的用于通信双方之间以 JSON 对象的形式安全传递信息的方法。JWT 可以使用 HMAC 算法或者是 RSA 的公钥密钥对进行签名。

本实例是使用github开源项目来实现，项目地址为
[点击查看源jwt项目代码](https://github.com/lcobucci/jwt/tree/3.2)

怎么使用jwt项目？
- 1.阅读GitHub上项目文档说明
- 2.composer安装jwt（composer require lcobucci/jwt）

#### 实例演示token签名并创建token
```
<?php
include "./vendor/autoload.php";
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

$time = time();
$token = (new Builder())->setHeader('alg','HS256')
//配置发行者
->setIssuer("jwtTest")
//配置Audience
->setAudience("php")
//配置令牌发出的时间（签发时间）
->setIssuedAt($time)
//配置令牌该时间之前不接收处理该Token
->setNotBefore($time+60)
//配置令牌到期的时间
->setExpiration($time + 7200)
//配置一个自定义uid声明
->set('uid',20)
//使用sha256进行签名,密钥为123456
->sign(new Sha256(),"123456")
//获取token
->getToken();
// echo $token;
//获取设置所有header头
$headers = $token->getHeaders();    
//获取所有的配置
$claims = $token->getClaims();

$alg = $token->getHeader('alg');
$iss = $token->getClaim('iss');
$aud = $token->getClaim('aud');
$iat = $token->getClaim('iat');
$exp = $token->getClaim('exp');
$nbf = $token->getClaim('nbf');
$uid = $token->getClaim('uid');

echo "=====下面是设置的header头信息======<br/>";
echo "当前token的alg盐值为{$alg}<br/>";

echo "=====下面是所有配置信息<br/>";
echo "当前token的发行者是：{$iss}<br/>";
echo "当前token的Audience是：{$aud}<br/>";
echo "当前token的令牌发出时间是：{$iat}<br/>";
echo "当前token不接收处理的时间为:{$nbf}<br/>";
echo "当前token的到期时间：{$exp}<br/>";
echo "当前token的uid是：{$uid}<br/>";
echo "当前token字符串为:{$token}";
```
结果展示:
```
=====下面是设置的header头信息======
当前token的alg盐值为HS256
=====下面是所有配置信息
当前token的发行者是：jwtTest
当前token的Audience是：php
当前token的令牌发出时间是：1563246935
当前token不接收处理的时间为:1563246995
当前token的到期时间：1563254135
当前token的uid是：20
当前token字符串为:eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3RUZXN0IiwiYXVkIjoicGhwIiwiaWF0IjoxNTYzMjQ2OTM1LCJuYmYiOjE1NjMyNDY5OTUsImV4cCI6MTU2MzI1NDEzNSwidWlkIjoyMH0.O8tscKPweCvSaXkOVbmhtEcsJ7BWRxRn9s_xXFstgsE
```
#### 解析token并校验token合法性
```
<?php
include "./vendor/autoload.php";
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

//解析token
$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3RUZXN0IiwiYXVkIjoicGhwIiwiaWF0IjoxNTYzMjQ2Mjc4LCJuYmYiOjE1NjMyNDYzMzgsImV4cCI6MTU2MzI1MzQ3OCwidWlkIjoyMH0.1XSZW6aWrHplAlMPMpc1K5gKdpWYE7AMa6T7qhBTF30';

//解析后的token对象
$decodeToken = (new Parser())->parse((string) $token);
$uid = $decodeToken->getClaim('uid');
$exp = $decodeToken->getClaim('exp');
$time = time();
echo "当前时间为：{$time}<br/>";
$result = $decodeToken->verify(new Sha256(),"123456");

//对token进行发行者和audience的校验
$data = new ValidationData();
$data->setAudience("php");
$data->setIssuer("jwtTest");
$resultVali =  $decodeToken->validate($data);
// print_r($resultVali);

//判断token签名和校验发行者是否合法
if($result && $resultVali){
    //如果设置的token有效时间大于当前时间则表示token过期了
    if($time>$exp){
        echo "该token已经过期了<br/>";    
    }else{
        echo "该token是合法的uid为:{$uid}，过期时间为{$exp}<br/>";
    }
}else{
    echo "该token不合法签名错误或发行者不合法<br/>";
}
```
运行后结果为：
```
当前时间为：1563248373
该token是合法的uid为:20，过期时间为1563253478
```
#### 类库封装管理jwt实例
```
<?php
include "./vendor/autoload.php";
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Claim\GreaterOrEqualsTo;
use Lcobucci\JWT\Token;

/**
 * Class JwtAuth
 * @package App\Library
 * @desc jwt接口鉴权类处理
 */
class JwtAuth{
    //jwt token
    private $token;
    /**
     * @var 用户传递的decode token
     */
    private $decodeToken;
    private static $_instance;
    private $iss = "jwtTest";
    private $aud = "php";
    private $uid;
    private $serect = "123456";
    private $exp;//token失效时间
    const EXP = 60;//token有效时间一个月

    /**
     * JwtAuth constructor.
     * 私有化construct方法
     */
    private function __construct()
    {
    }

    /**
     * 私有化clone方法
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     *  获取jwtauth类实例化对象
     */
    public static function getInstance(){
        if(!(self::$_instance instanceof JwtAuth) ){
            self::$_instance = new JwtAuth();
        }
        return self::$_instance;
    }

    /**
     * 获取token
     * @return string
     */
    public function getToken(){
        return (string)$this->token;
    }

    /**
     * 设置token
     * @param $token
     * @return $this
     */
    public function setToken($token){
        $this->token = $token;
        return $this;
    }

    /**
     * 设置uid
     * @param $uid
     * @return $this
     */
    public function setUid($uid){
        $this->uid = $uid;
        return $this;
    }

    /**
     * 获取uid
     * @return mixed
     */
    public function getUid(){
        return $this->uid;
    }

     /**
     * 获取exp
     * @return mixed
     */
    public function getExp(){
        return $this->exp;
    }

    /**
     * 编码jwt token
     */
    public function encode(){
        $time = time();
        $this->token = (new Builder())->setHeader('alg','HS256')
            ->setIssuer($this->iss)
            ->setAudience($this->aud)
            ->setIssuedAt($time)
            ->setExpiration($time + self::EXP)
            ->set('uid',$this->uid)
            ->sign(new Sha256(),$this->serect)
            ->getToken();
        return $this;
    }

    /**
     * 解析传递的token
     * @return 用户传递的decode
     */
    public function decode(){
        if(!$this->decodeToken){
            // Parses from a string
            $this->decodeToken = (new Parser())->parse((string) $this->token);
            $this->uid = $this->decodeToken->getClaim('uid');
            $this->exp = $this->decodeToken->getClaim('exp');
//            error_log('exp:'.$this->decodeToken->getClaim('exp'));
//            error_log('uid:'.$this->decodeToken->getClaim('uid'));
        }
        return $this->decodeToken;
    }

    /**
     * verify校验token signature串第三个字符串
     * @return mixed
     */
    public function verify(){
        $result = $this->decode()->verify(new Sha256(),$this->serect);
        return $result;
    }

    /**
     * 校验传递的token是否有效,校验前两个字符串
     * @return mixed
     */
    public function validate(){
        $data = new ValidationData();
        $data->setAudience($this->aud);
        $data->setIssuer($this->iss);
        return $this->decode()->validate($data);
    }

}

/*
@desc 模拟登录返回token
 */
$jwtAuth = JwtAuth::getInstance();
$uid = 20;
$token = $jwtAuth->setUid($uid)->encode()->getToken();
echo "当前已经为您生成最新token:{$token}<br/>";


/*
1.解析token
2.校验token签名是否合法
3.验证token发行者等信息是否合法
4.校验token是否过期
 */
$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3RUZXN0IiwiYXVkIjoicGhwIiwiaWF0IjoxNTYzMjY4NjExLCJleHAiOjE1NjMyNjg2NzEsInVpZCI6MjB9.PeEA3xTE2lKl4YCYQ2cjHSNYsrJ24HRnW1-yKM-LgHc';
$jwtAuth2 = JwtAuth::getInstance();
$jwtAuth2->setToken($token);
if($jwtAuth2->validate() && $jwtAuth2->verify()){
    //初始化用户id
    $uid = $jwtAuth2->getUid();
    $exp = $jwtAuth2->getExp();
    echo "token合法,您当前的uid为:{$uid},当前时间戳为:".time()."token有效时间为:{$exp}<br/>";
}else{
    echo "token校验失败，token签名不合法，或token发行者信息不合法<br/>";
}
```
演示结果为
```
当前已经为您生成最新token:eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3RUZXN0IiwiYXVkIjoicGhwIiwiaWF0IjoxNTYzMjY4NjIzLCJleHAiOjE1NjMyNjg2ODMsInVpZCI6MjB9.BrsVElhVkTIq5xH3-JpvqvawNhDALb98VYZGbMTzWV8
token合法,您当前的uid为:20,当前时间戳为:1563268623token有效时间为:1563268671
```
```
当前已经为您生成最新token:eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3RUZXN0IiwiYXVkIjoicGhwIiwiaWF0IjoxNTYzMjY4NzA1LCJleHAiOjE1NjMyNjg3NjUsInVpZCI6MjB9.juTM5iG8LNDid8Sp4jOjtHeTitaIB2WxZeW3GjnQrB0
token校验失败，token签名不合法，或token发行者信息不合法
```


