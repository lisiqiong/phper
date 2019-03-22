# php面相对象知识整理
* [对象引用](#对象引用)
* [访问控制private](#访问控制private)
* [对象遍历](#对象遍历)


## 访问控制private
#### 私有属性内部调用
```
<?php
class Person {
	/**
	 * 私有属性内部调用不会调用__set,__get方法
	 ***/
	public $name;
	private $age;
	private $status;

	public function __construct($name, $age, $status) {
		$this->name = $name;
		$this->age = $age;
		$this->status = $status;
	}

	/**
	 * @desc 获取用户信息
	 **/
	public function getUserInfo() {
		return $this->name . "今年" . $this->age . "岁," . '婚姻状态:' . $this->status;
	}

	//给私有属性变量赋值
	public function __set($key, $value) {
		if ($key == 'age') {
			$this->$key = $value + 2;
		} else {
			$this->$key = $value;
		}
	}

	//获取私有变量
	public function __get($key) {
		return $this->$key;
	}

}

$obj = new Person("巴八灵", 28, "已婚");
echo $obj->getUserInfo();
/** 运行结果
巴八灵今年28岁,婚姻状态:已婚
 **/

```
#### 私有属性外部调用
```
<?php
class Person {
	/**
	 * 私有属性外部设置获取需要通过__set,__get方法可以设置和获取属性的信息
	 ***/
	public $name;
	private $age;
	private $status;

	/**
	 * @desc 获取用户信息
	 **/
	public function getUserInfo() {
		return $this->name . "今年" . $this->age . "岁," . '婚姻状态:' . $this->status;
	}

	//给私有属性变量赋值
	public function __set($key, $value) {
		if ($key == 'age') {
			$this->$key = $value + 2;
		} else {
			$this->$key = $value;
		}
	}

	//获取私有变量
	public function __get($key) {
		return $this->$key;
	}

}

$obj = new Person();
$obj->name = '巴八灵';
$obj->age = 28;
$obj->status = "已婚";
echo "age设置后的值" . $obj->age . PHP_EOL;
echo $obj->getUserInfo();

/** 运行后结果
age设置后的值30
巴八灵今年30岁,婚姻状态:已婚
 **/

```

## 对象遍历
```
<?php
/**
 * 简单的遍历对象只能够遍历public权限的变量,protected与private无法遍历
 **/
class CateData {
	public $val1 = "value1";
	public $val2 = "value2";
	public $val3 = "value3";
	protected $val4_protected = "this is protected val";
	private $val5_private = "this is private val";
	public function __construct() {
	}

}

$obj = new CateData();

foreach ($obj as $key => $value) {
	echo $key . '--' . $value . PHP_EOL;
}
/**
输出结果
val1--value1
val2--value2
val3--value3
 **/
```

## 对象引用
#### 对象赋值
```
<?php
/**
@desc php5之后对象变量将不再保存整个对象的值，而只是保存一个标识符来访问真正的对象内容。
当变量作为参数传递，作为结果返回都是保存着同一个标识符的拷贝。
**/
class Car
{
	public $brand;
}
$car1 = new Car();//$car1存的是对象的标识符
$car2 = $car1;//$car1与$car2拥有同一个对象的标识符
$car3 = new Car();
var_dump($car1);
var_dump($car2);
var_dump($car3);
$car2->brand = '宝马';
$car1->brand = '奥迪';
echo $car1->brand.PHP_EOL;
echo $car2->brand.PHP_EOL;
```
结果为：
```
object(Car)#1 (1) {
  ["brand"]=>
  NULL
}
object(Car)#1 (1) {
  ["brand"]=>
  NULL
}
object(Car)#2 (1) {
  ["brand"]=>
  NULL
}
奥迪
奥迪
```
> 赋值后发现对象都为#1标示，再重新创建一个对象实例变量对象标示变为了#2,同一个标示指向相同的信息修改会影响
#### 赋值引用对象
```
<?php
class Car
{
	public $brand;
}
$car1 = new Car();
$car2 = &$car1;
var_dump($car1);
var_dump($car2);
$car1->brand = '奥迪';
echo $car2->brand.PHP_EOL;
echo $car1->brand.PHP_EOL;

```
运行结果为：
```
object(Car)#1 (1) {
  ["brand"]=>
  NULL
}
object(Car)#1 (1) {
  ["brand"]=>
  NULL
}
奥迪
奥迪
```
> 赋值引用对象都指向同一个对象地址，修改赋值相互有影响