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