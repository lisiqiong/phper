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
