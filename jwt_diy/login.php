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
