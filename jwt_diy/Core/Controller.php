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