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


