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



