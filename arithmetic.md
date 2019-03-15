# php实现几种常见算法
* 排序
 * [冒泡排序](#冒泡排序)
 * [快速排序](#快速排序)
 * [选择排序](#选择排序)
* 查找
 * [二分法查找](#二分法查找)
 * [递归](#递归)
 * [顺序查找](#顺序查找)
* 其它
 * [乘法口诀](#乘法口诀)
 * [寻最小的n个数](#寻最小的n个数)
 * [寻相同元素](#寻相同元素)
 * [抽奖](#抽奖)
 * [数组反转](#数组反转)
 * [随机打乱数组](#随机打乱数组)
 * [寻找最小元素](#寻找最小元素)

### 公共方法
```
//创建数据
function createData($num) {
    $arr = [];
    for ($i = 0; $i < $num; $i++) {
        $arr[$i] = rand($i, 1000);
    }
    return $arr;
}

//打印输出数组
function printSortArr($fun, $num = 10) {
    $data = createData($num);
    $dataString = implode(',', $data);
    echo "原数据:{$dataString}" . PHP_EOL;
    $arr = $fun($data);
    echo "算法[{$fun}]数据打印" . PHP_EOL;
    foreach ($arr as $key => $value) {
        # code...
        echo $value . PHP_EOL;
    }
}

```

### 冒泡排序
```
<?php

/**
 @desc 冒泡排序(像水里的气泡一样最大的先出来，依次次大的出来，最后全部排序成功)
 原理：1.循环大一轮两辆比较将最大的一个数排到末尾结束
 2.循环第二轮，不过需要注意的是这个时候元素已经变成了除去最大数之外的循环，循环同上
 3.依次类推最后依次将最大的拍到后面，所有循环结束，则数据就是从大到小的排序
 **/

//冒泡排序
function mp_sort($arr) {
    $count = count($arr);
    for ($i = 0; $i < $count; $i++) {
        for ($j = 1; $j < $count - $i; $j++) {
            if ($arr[$j - 1] > $arr[$j]) {
                $temp = $arr[$j - 1];
                $arr[$j - 1] = $arr[$j];
                $arr[$j] = $temp;
            }
        }
    }
    return $arr;
}



printSortArr('mp_sort', 10);
```
### 快速排序 

```
<?php

/***
 *@desc 快速排序
 *思想:通过一趟排序将要排序的数据分隔成独立的两部分，其中一部分的所有数据都比另外一部分的所有数据都要小，
 然后再按此方法对这两部分的数据分别进行快速排序，整个排序过程可以递归进行，以此达到整个数据变成有序序列。
 **/
function quickSort(&$arr){
    if(count($arr)>1){
        //定义一基准数,再定义一个小于基准数的数组，和一个大于基准数的数组，然后再递归进行快速排序
        $k = $arr[0];//定基准数
        $x = [];//小于基准数的数组
        $y = [];//大于基准数的数组
        $size = count($arr);
        for($i=1;$i<$size;$i++){
            if($arr[$i]>$k){
                $y[] = $arr[$i];
            }elseif($arr[$i]<=$k){
                $x[] = $arr[$i];
            }
        }
        $x = quickSort($x);
        $y = quickSort($y);
        return array_merge($x,array($k),$y); 
    }else{
        return $arr;
    }
}

$arr = [2,78,3,23,532,13,67];
print_r(quickSort($arr));

```
##### 输出结果
```
原数据:504,480,612,677,613,395,506,129,479,605
算法[mp_sort]数据打印
129
395
479
480
504
506
605
612
613
677
```
### 选择排序 
```
<?php

/**
 *@desc 选择排序
 *原理：每一次从待排序的数据元素中选出最小（或最大）的一个元素，存放在序列的起始位置，知道全部待排序的数据元素排完。
 **/
function selectSort($array)
{
    $count = count($array);
    for($i=0;$i<$count-1;$i++){
        $min = $i;
        //找出最小值的索引
        for($j=$i+1;$j<$count;$j++){
            if($array[$min]>$array[$j]){
                $min = $j;
            }
        }
        if($min!=$i){
            //$temp = $array[$min];
            //$array[$min] = $array[$i];
            //$array[$i] = $temp;
            $temp = $array[$i];
            $array[$i] = $array[$min];
            $array[$min] = $temp;

        } 
        
    }
    return $array;
}

$array = [10,56,12,36,8,9,11,23,451,253];

$res = selectSort($array);
print_r($res);

```
### 二分法查找 

```
<?php

/***
 *@desc 二分法查找
 *条件：要求数组是有序的数组
 *原理：每次查找取中，跟要查找的目的数进行比较，如果小则在数组的开始端和结束端取中进行比较，如果
 如果大则中间段跟数组结尾端再次取中进行比较，依次类推。
 ***/
function dichotomyFindvalue($arr,$num){
    $end = count($arr);
    $start = 0;
    $middle = floor(($start+$end)/2);
    $i = 0;
    while($start<$end-1){
       if($arr[$middle]==$num){
            $i++;
            return [$middle+1,$i];
       }elseif($arr[$middle]<$num){
            $start = $middle;
            $middle = floor(($start+$end)/2);
       }else{
            $end = $middle;
            $middle = floor(($start+$end)/2);
       }
       $i++;
    }
    return false;
}


$arr = [
    24,37,42,59,69,78,82,84,91,93,96,102,103,106,113,116,117,118,125,128,130,131,133,138,139,140,142,144,146,150,155,156,157,158,166,167,168,
    172,174,175,177,178,179,181,186,187,189,190,191,192,194,198,199,200,201,202,204,205,206,207,213,218,220,223,224,226,227,228,230,231,
    232,233,236,238,241,242,244,245,246,247,249,251,252,255,257,258,259,260,263,264,265,266,267,268,270,272,273,274,275,278,280,281,282,
    283,285,286,287,288,290,291,292,297,299,300,302,304,305,306,307,308,309,310,311,312,313,314,315,317,318,319,320,321,324,325,326,327,
    328,332,334,336,337,338,339,340,341,342,343,344,346,347,348,349,350,351,352,353,354,356,357,358,360,361,362,363,364,365,366,367,368,
    369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,
    402,403,404,405,406,407,409,410,411,412,413,414,415,416,417,418,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,437,
    438,439,440,441,442,443,444,445,446,447,449,451,452,453,454,455,456,457,458,460,461,462,463,464,465,467,469,470,472,473,474,477,478,
    479,481,483,484,485,487,488,489,491,495,501,505,506,508,509,517,520,522,523,528,532,533,542,543,551,553,554,563,571,576,577,583,594
];

//数组要查找的数组的位置
$num = 312;//查找数字在数组中的第多少个位置
$res = dichotomyFindvalue($arr,$num);
if($res){
    echo "数字".$num.'在数组中第'.$res[0].'个中被找到,该查找共循环次数:'.$res[1];
    exit;
}
echo "要查询的数字不在数组中";
```
### 递归
```
<?php

$areaList = [
    ['id'=>1,'name'=>'湖北省','pid'=>0,'son'=>''],
    ['id'=>2,'name'=>'广东省','pid'=>0,'son'=>''],
    ['id'=>3,'name'=>'湖南省','pid'=>0,'son'=>''],
    ['id'=>4,'name'=>'武汉市','pid'=>1,'son'=>''],
    ['id'=>5,'name'=>'荆州市','pid'=>1,'son'=>''],
    ['id'=>6,'name'=>'宜昌市','pid'=>1,'son'=>''],
    ['id'=>7,'name'=>'咸宁市','pid'=>1,'son'=>''],
    ['id'=>8,'name'=>'仙桃市','pid'=>1,'son'=>''],
    ['id'=>9,'name'=>'潜江市','pid'=>1,'son'=>''],
    ['id'=>10,'name'=>'深圳市','pid'=>2,'son'=>''],
    ['id'=>11,'name'=>'广州市','pid'=>2,'son'=>''],
    ['id'=>12,'name'=>'珠海','pid'=>2,'son'=>''],
    ['id'=>13,'name'=>'佛山市','pid'=>2,'son'=>''],
    ['id'=>14,'name'=>'长沙市','pid'=>3,'son'=>''],
    ['id'=>15,'name'=>'岳阳市','pid'=>3,'son'=>''],
    ['id'=>16,'name'=>'株洲市','pid'=>3,'son'=>''],
    ['id'=>17,'name'=>'衡阳市','pid'=>3,'son'=>''],
];

function recursive($arr,$pid=0){
    $tree = [];
    foreach($arr as $k=>$v){
       if($v['pid'] == $pid){
           $v['son'] = recursive($arr,$v['id']); 
           $tree[] = $v;
       }    
        
    }
    return $tree;
}
print_r(recursive($areaList));
```
### 顺序查找
```
<?php

/**
 *@desc 顺序查找某个数是否在数组中
 顺序查找：即循环查找
 **/

$arr = [2,100,89,67,34,78,900,234,675];
$findNum = 34;
$flag = false;
foreach($arr  as $k=>$v){
    if($v==$findNum){
        echo "查到了，键值为$k";
        $flag = true;
        break;
    }
}
if($flag==false){
    echo "未找到该数";
}

```
### 乘法口诀
```
<?php
/**
 *@desc 乘法口诀
 **/
for($i=1;$i<=9;$i++){
    for($j=1;$j<=$i;$j++){
        if($j==$i){
            echo $j.'*'.$i.'='.$j*$i."\n";
        }else{
            echo $j.'*'.$i.'='.$j*$i.'  ';
        }
      }
}
echo "\n\n---------------------------\n\n";
$word = ['1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'七','8'=>'八','9'=>'九',
    '10'=>'十','12'=>'十二','14'=>'十四','15'=>'十五','16'=>'十六','18'=>'十八','20'=>'二十','21'=>'二十一','24'=>'二十四','25'=>'二十五','27'=>'二十七','28'=>'二十八','30'=>'三十',
   '31'=>'三十一', '32'=>'三十二','35'=>'三十五','36'=>'三十六','40'=>'四十','42'=>'四十二','45'=>'四十五','48'=>'四十八','49'=>'四十九','52'=>'五十二','54'=>'五十四','56'=>'五十六',
    '63'=>'六十三','64'=>'六十四','72'=>'七十二','81'=>'八十一'
];
for($i=1;$i<=9;$i++){
    for($j=1;$j<=$i;$j++){
        $num = $j*$i;
        if($j==$i){
            echo $word[$j].$word[$i].'得'.$word[$num]."\n";
        }else{
            echo $word[$j].$word[$i].'得'.$word[$num].'    ';
        }
        unset($num);
      }
}
```
### 寻最小的n个数
```
<?php
/**
 *@desc 选择数组中最小的n个数
 ***/
function get_min_array($arr, $k)
{
    $n = count($arr);
    $min_array = array();
    for ($i = 0; $i < $n; $i++) {
        if ($i < $k) {
            $min_array[$i] = $arr[$i];
        } else {
            if ($i == $k) {
                $max_pos = get_max_pos($min_array);
                $max = $min_array[$max_pos];
            }
            if ($arr[$i] < $max) {
                $min_array[$max_pos] = $arr[$i];
                $max_pos = get_max_pos($min_array);
                $max = $min_array[$max_pos];
            }
        }
    }
    return $min_array;
}
/* 获取数组中值最大的位置
* @param array $arr
* @return array
    */
function get_max_pos($arr)
{
    $pos = 0;
    //echo '$pos:'.$pos.PHP_EOL;
    for ($i = 1; $i < count($arr); $i++) {
        if ($arr[$i] > $arr[$pos]) {
            //echo '$i:'.$i.'--$post:'.$pos.PHP_EOL;
            $pos = $i;
        }
    }
    return $pos;
}
$array = [1, 100, 20, 22, 33, 44, 55, 66, 23, 79, 18, 20, 11, 9, 129, 399, 145,88,56,84,12,17];
//$min_array = get_min_array($array, 10);
//print_r($min_array);
$arr = [130,2,4,100,89,8,99];
$num = get_max_pos($arr);
echo $num;
print_r($arr[$num]);

```
### 寻相同元素
```
<?php
$arr1 = [2,4,5,7,8,9,17];
$arr2 = [6,7,9,12,15,16,17];
/**
 *@desc 找出两个有序数组的相同元素出来
 **/
function findCommon($arr1,$arr2)
{
    $sameArr = [];
    $i = $j = 0;
    $count1 = count($arr1);
    $count2 = count($arr2);
    while($i<$count1 && $j<$count2){
        if($arr1[$i]<$arr2[$j]){
            $i++;
        }elseif($arr1[$i]>$arr2[$j]){
            $j++;
        }else{
            $sameArr[] = $arr1[$i];
            $i++;
            $j++;
        }
    }
    if(!empty($sameArr)){
        $sameArr = array_unique($sameArr);
    }
    return $sameArr;
}
$result = findCommon($arr1,$arr2);
print_r($result);
```
### 抽奖
```
<?php
$arr = [
    ['id'=>1,'name'=>'特等奖','v'=>1],
    ['id'=>2,'name'=>'二等奖','v'=>3],
    ['id'=>3,'name'=>'三等奖','v'=>5],
    ['id'=>4,'name'=>'四等奖','v'=>20],
    ['id'=>5,'name'=>'谢谢参与','v'=>71],
];
function draw($arr)
{
    $result = [];
    //计算总抽奖池的积分数
    $sum = [];    
    foreach($arr as $key=>$value){
       $sum[$key] = $value['v'];
    }
    $randSum = array_sum($sum);
    $randNum = mt_rand(1,$randSum);
    error_log('随机数数：'.$randNum);
    $count = count($arr);
    $s = 0;
    $e = 0;
    for($i=0;$i<$count;$i++){
        if($i==0){
            $s = 0;
        }else{
            $s += $sum[$i-1];
        }
        $e += $sum[$i];
        if($randNum>=$s && $randNum<=$e){
            $result = $arr[$i];      
        }
    }
    unset($sum);
    return $result;
}
print_r(draw($arr));
```
### 数组反转
```
<?php
$arr = ['好','好','学','习','天','天','向','上'];
/**
 *@desc 将一维数组进行反转
 ***/
function reverse($arr)
{
   $n = count($arr);
   $left = 0;
   $right = $n-1;
   $temp = [];
   //首尾依次替换元素的值实现数组反转
   while($left<$right){
       $temp = $arr[$left];
       $arr[$left] = $arr[$right];
       $arr[$right] = $temp;
       $left++;
       $right--;
   }
   return $arr;
}
$result = reverse($arr);
print_r($result);
```
### 随机打乱数组
```
<?php
/***
 *@desc 将一个数组中的元素随机打算
 **/
function randShuffle($arr)
{
   $n = count($arr);
   $temp = [];
   for($i=0;$i<$n;$i++){
       $randNum = rand(0,$n-1);
       if($randNum!=$i){
           $temp = $arr[$i];
           $arr[$i] = $arr[$randNum];
           $arr[$randNum] = $temp;
           unset($temp);
       }   
   }
   return $arr;
}
$arr = ['a','c','test','cofl',1,9,48];
$result = randShuffle($arr);
//print_r($result);
/*
 *@desc 洗牌算法
 **/
function wash_card($cardNum){
   $n = count($cardNum);
   $temp = [];
   for($i=0;$i<$n;$i++){
       $randNum = rand(0,$n-1);
       if($randNum!=$i){
           $temp = $cardNum[$i];
           $cardNum[$i] = $cardNum[$randNum];
           $cardNum[$randNum] = $temp;
           unset($temp);
       }   
   }
   return $arr;
}
$cardNum = [];
```
### 寻找最小元素
#### 方式一
```
<?php
$arr = [9,56,789,45,35,12,2,88,852,963,456,785,123,456,852,423,965,3,444,555,654,743,982];
$count = count($arr);
$minValue = 0;
$minIndex = 0;
for($i=0;$i<$count;$i++){
    $n = $i+1;
    if($n<$count){
        if($arr[$minIndex]<$arr[$n]){
            $minIndex = $minIndex;
            $minVlaue = $arr[$minIndex];
        }else{
            $minIndex = $n;
            $minValue = $arr[$n];
        }
    }
}
echo "最小值的索引为：".$minIndex.',最小值为:'.$minValue;
```

#### 方式二
```
<?php
$arr = [10,15,2,7,8];
$count = count($arr);
$min = 0;//最小元素索引值标示
for($i=1;$i<$count;$i++){
    if($arr[$min]>$arr[$i]){
        $min = $i;
    }
}
echo "数组中最小值的索引为:".$min.',最小值为:'.$arr[$min];
```


