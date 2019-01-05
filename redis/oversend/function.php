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