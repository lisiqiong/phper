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