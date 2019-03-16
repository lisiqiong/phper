# php面相对象知识整理
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

