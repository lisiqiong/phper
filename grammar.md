# php语法基础整理
+ [运算符++](#运算符++)
+ [数组指针](#数组指针)
+ 文件操作管理
+ 图片操作
+ 字符串操作

## 数组指针
### 1.介绍几个数组指针的函数
- current() - 返回数组中的当前单元
- end() - 将数组的内部指针指向最后一个单元
- prev() - 将数组的内部指针倒回一位
- reset() - 将数组的内部指针指向第一个单元
- each() - 返回数组中当前的键／值对并将数组指针向前移动一步


```
<?php
$listArr = [
    '1232','2456','7789','8976',
    '5678','3456','2347','9876',
    '3451','7744','2212','3214',
];


echo "第一个元素".key($listArr).'=>'.current($listArr).PHP_EOL;
next($listArr);
echo  "第二个元素".key($listArr).'=>'.current($listArr).PHP_EOL;
next($listArr);
echo  "第三个元素".key($listArr).'=>'.current($listArr).PHP_EOL;
end($listArr);
echo "最后一个元素".key($listArr).'=>'.current($listArr).PHP_EOL;
prev($listArr);//内部指针倒回一位
echo "倒数第二位".key($listArr).'=>'.current($listArr).PHP_EOL;
reset($listArr);
echo "第一个元素".key($listArr).'=>'.current($listArr).PHP_EOL;

```

#### 输出结果
```
第一个元素0=>1232
第二个元素1=>2456
第三个元素2=>7789
最后一个元素11=>3214
倒数第二位10=>2212
第一个元素0=>1232
```

### 2.使用each循环数组
```
<?php
$listArr = [
    '1232','2456','7789','8976',
    '5678','3456','2347','9876',
    '3451','7744','2212','3214',
];

//使用each方法遍历数组

reset($listArr);
while(list($key,$value) = each($listArr)){
    echo "key:{$key},value:{$value}".PHP_EOL;
}
```
#### 输出结果
```
key:0,value:1232
key:1,value:2456
key:2,value:7789
key:3,value:8976
key:4,value:5678
key:5,value:3456
key:6,value:2347
key:7,value:9876
key:8,value:3451
key:9,value:7744
key:10,value:2212
key:11,value:3214
```


### 3.使用数组指针取出当前值的下一个值，环形取
```
<?php

/**
 * 使用数组的指针函数实现数据类循环队列读取数据
 */
$listArr = [
    '1232','2456','7789','8976',
    '5678','3456','2347','9876',
    '3451','7744','2212','3214',
];

/**
 * [getNextvalue 根据当前值获取数组值下一个值]
 * @Author   lisiqiong
 * @DateTime 2019-04-25
 * @param    [type]     $value [description]
 * @return   [type]            [description]
 */
function getNextvalue($listArr,$value){
    $count = count($listArr);
    $keyArr = array_keys($listArr,$value);
    $key = $keyArr[0];
    if(($key+1)!=$count){
        for($i=0;$i<=$key;$i++){
            next($listArr);
        }
    }
    return current($listArr);
}

$value = getNextvalue($listArr,'2456');
echo "2456的下一个是{$value}".PHP_EOL;

$value = getNextvalue($listArr,'3214');
echo "3214的下一个是{$value}".PHP_EOL;
```

#### 运行结果
```
2456的下一个是7789
3214的下一个是1232
```
