   # PHP设计模式
   + [单例模式](#单例模式)
   + [观察者模式](#观察者模式)
   + [简单工厂模式](#简单工厂模式)
   + [建造者模式](#建造者模式)
   + [策略模式](#策略模式)

## 单例模式
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

    //只有这个入库可以获取db的实例
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
运行结果

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

总结：
* db1，db2实现化后是两个不同的实例，#1，#2
* db3,db4调用的单例的实例化方法，获取的实际上是一个实例，#3
* 单例模式需要将构造方法设置为私有，防止外面生成新的实例，需要将clone方法设置为私有防止克隆
* 定义一个变量存储对象实例，如果对象实例是属于类创建的则直接返回，否则重新生成，保持类的实例只有一个存在
