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
