# PHP设计模式
### 什么是设计模式？ 
1. 设计模式是一套被反复使用、多数人知晓的、经过分类的、代码设计经验的总结。使用设计模式是为了可以重用代码，让代码更容易被他人理解，保证
代码的可靠性。

2. 设计模式是一种思想，跟编程语言关系不大，尽管不同语言实现设计模式不尽相同，但是思想核心是一致的。他们都要遵循设计模式的六大规则，设计
模式不一定就是完完整整的按照案例书写的，只要符合设计模式的六大规则就是好的设计模式。

3. 设计模式的六大规则
  - 单一职责：一个类只负责一项职责。
  - 里氏代换：子类可以扩展父类的功能，但不能够改变父类原有的功能。
  - 依赖倒置：高层模块不应该依赖底层模块，二者都应该依赖抽象；抽象不应该依赖细节；细节应该依赖抽象。
  - 接口隔离：一个类对另一个类的依赖应该建立在最小的接口上。
  - 迪米特法则：一个对象应该对其它对象保持最少的了解。
  - 开闭原则：一个软件实体的模块或函数，对实体扩展开放 对实体修改关闭。

---

### 下面用php实现如下的设计模式
+ [单例模式](#单例模式)
+ [简单工厂模式](#简单工厂模式)
+ [观察者模式](#观察者模式)
+ [建造者模式](#建造者模式)
+ [策略模式](#策略模式)
+ [责任链模式](#责任链模式)
+ [对象映射模式](#对象映射模式)

## 单例模式
---
```
<?php

class Mysql{

    public function __construct(){

    }

    public function getInfo(){
        return 'the mysql version is 5.7. ';
    }

}

$db1 = new Mysql();
var_dump($db1);
echo $db1->getInfo();
echo PHP_EOL;
$db2 = new Mysql();
var_dump($db2);
echo $db2->getInfo();
echo PHP_EOL;

class Db{

    private static $_instance;

    private function __construct(){

    }

    //私有化克隆方法
    private function __clone(){

    }

    //只有这个入口可以获取db的实例
    //instanceof 用于确定一个 PHP 变量是否属于某一类 class 的实例：
    public  static function getInstance(){
        if(!(self::$_instance instanceof Db) ){
            self::$_instance = new Db();
        }
        return self::$_instance;
    }

    //获取数据库信息
    public function getInfo(){
        return 'the database is mysql';
    }

}

$db3 = Db::getInstance();
echo $db3->getInfo();
var_dump($db3);
echo PHP_EOL;
$db4 = Db::getInstance();
echo $db4->getInfo();
var_dump($db4);



```
#### 运行结果

```
object(Mysql)#1 (0) {
}
the mysql version is 5.7. 
object(Mysql)#2 (0) {
}
the mysql version is 5.7. 
the database is mysqlobject(Db)#3 (0) {
}

the database is mysqlobject(Db)#3 (0) {
}
```

#### 总结：
* db1，db2实现化后是两个不同的实例#1，#2
* db3,db4调用的单例的实例化方法，获取的实际上是一个实例#3
* 单例模式需要将构造方法设置为私有，防止外面生成新的实例，需要将clone方法设置为私有防止克隆
* 定义一个变量存储对象实例，如果对象实例是属于类创建的则直接返回，否则重新生成，保持类的实例只有一个存在

## 简单工厂模式
---
```
<?php

/**
*@desc 数据库接口类
**/
interface Db{
    public function connect();
    public function insert();
    public function delete();
    public function edit();
    public function find();
}
 
/***
*@desc mysql数据库操作类
**/ 
class Mysql implements Db{

    public function connect(){
        echo "mysql connect ok";
    }

    public function insert(){

    }

    public function delete(){

    }

    public function edit(){

    }

    public function find(){

    }


}

/**
*@desc sqlite数据库操作类
***/
class Sqlite implements Db{

    public function connect(){
        echo "sqlite connect ok";
    }

    public function insert(){

    }

    public function delete(){

    }

    public function edit(){

    }

    public function find(){

    }

}

/**
*@desc 工厂类
**/
class Factory{
    static $db = null;
    public static function createFactory($dbType){
        switch($dbType){
            case 'mysql':
                self::$db = new Mysql();
                break;
            case 'sqlite':
                self::$db = new Sqlite();
                break;
        }
        return self::$db;
    }
}


$db = Factory::createFactory('mysql');
$db->connect();

echo PHP_EOL;

$db = Factory::createFactory('sqlite');
$db->connect();

```
#### 运行结果
```
mysql connect ok
sqlite connect ok
```

#### 总结
1. 只需要传递一个参数就可以实例化想要的类
2. 使用者没有感知，可以统一数据库操作的方法
3. 方便数据库切换


## 观察者模式
---
#### 概念
观察者模式有时候被称为发布订阅模式，或者模型视图模式，在这个模式中，当一个对象生改变的时候，依赖它的对象会全部收到通知，并自动更新。
#### 使用场景
一件事情发生以后，要执行一连串的更新操作，传统的方法就是就是在事件之后加入直接加入处理逻辑，当更新的逻辑变的多了后，变变得难以维护，这种方式是耦合的，侵入式的，增加新的逻辑需要修改主题代码。
#### 实现方式
需要实现两个角色，观察者和被观察者对象。当被观察者发生改变的时候观察者观察者就会观察到这样的变化。

```
<?php

/**
*@desc 观察者
**/
interface Observer
{
    function update($lesson,$time);
}

/**
*@desc 被观察者抽象类
抽象类中不是必须有抽象方法，但是如果一个类有申明了抽象方法，那么这个类必须是抽象类，并且抽象类是不能够直接实例化的，如果要使用必须被继承
**/
abstract class Observable
{
    private $_obj = [];

    //添加观察者
    public function addObserver(Observer $observer){
        $this->_obj[] = $observer;
    }

    //通知观察者
    public function notify($lesson=null,$time=null){
        //遍历所有观察者通知对应的观察者
        foreach ($this->_obj as $observer) {
            $observer->update($lesson,$time);
        }
    }

}

/**
*因为定义的观察者抽象类不能够直接实例化，所以需要创建一个观察者来继承该类
*@desc 建立真实观察者对象
**/
class TrueObservable extends Observable
{
    private $_lesson='python';
    public function trgger($lesson='',$time='')
    {
        $this->_lesson = $lesson;
        $this->notify($lesson,$time);
    }
}

/***
*@desc 创建观察者对象老师
**/
class Obteacher implements Observer
{
    public $name;
    public function __construct($name){
        $this->name = $name;
    }
    public function update($lesson = '',$time=''){
        echo "老师".$this->name."您好，您".$time."的".$lesson."即将开始直播，提醒您做好必要的准备工作".PHP_EOL;
    }
} 


/***
*@desc 创建观察者对象学生
**/
class Obstudent implements Observer
{
    public $name;
    public function __construct($name){
        $this->name = $name;
    }
    public function update($lesson = '',$time=''){
        echo "学生".$this->name."你好，你预约的课程".$lesson."开始时间为".$time."请按时到达，祝你学习愉快".PHP_EOL;
    }
}

$obj = new TrueObservable();
$obj->addObserver(new Obteacher('张学友'));
$obj->addObserver(new Obstudent('刘德华'));
$obj->trgger('《php之设计模式》','2月19日下午2点');
```

#### 运行结果
```
老师张学友您好，您2月19日下午2点的《php之设计模式》即将开始直播，提醒您做好必要的准备工作
学生刘德华你好，你预约的课程《php之设计模式》开始时间为2月19日下午2点请按时到达，祝你学习愉快
```
#### 总结
该例子使用观察者模式，来实现当一个类的两个变量发生改变时，依赖它的对象会全部收到通知，并自动更新为当前的所传递的值的所属信息.






