# nginx相关配置说明
- [nginx信号量](#nginx信号量)
- nginx虚拟主机配置

## nginx信号量
信号说明

| 信号名称 | 作用 |
| ------ | ------ |
| TERM,INT | 快速关闭 |
| QUIT | 从容关闭 |
| HUP | 重新加载配置，用新的配置开始新的工作进程，从容关闭旧的工作进程 |
| USR1 | 重新打开日志文件 |
| USR2 | 平滑升级可执行程序 |
| WINCH | 从容关闭工作进程 |

#### hup信号优雅重启
a.html
```
<html>
<h>这里是a.html文件</h>
<script>
window.location.href='./';
</script>
</html>
```
index.html
```
<html>
<h>这里是index.html文件</h>
<script>
window.location.href='./';
</script>
</html>
```


查看当前nginx的配置文件
```
server{
        listen 80;
        server_name localhost;
        root /Users/lidong/www;
        index  index.html index.htm;
        access_log /Users/lidong/wwwlogs/access.log;
        error_log /Users/lidong/wwwlogs/error.log;
}
```
修改nginx的配置文件，将nginx设置为默认读取a.html
```
server{
        listen 80;
        server_name localhost;
        root /Users/lidong/www;
        index a.html index.html index.htm;
        access_log /Users/lidong/wwwlogs/access.log;
        error_log /Users/lidong/wwwlogs/error.log;
}
```

```
ps aux|grep nginx
lidong            5019   0.0  0.0  4339176   1136   ??  S    11:16上午   0:00.01 nginx: worker process  
lidong             352   0.0  0.0  4339176   1480   ??  S    五08上午   0:00.05 nginx: master process /usr/local/opt/nginx/bin/nginx -g daemon off;  
lidong            5284   0.0  0.0  4277252    824 s000  S+    2:04下午   0:00.01 grep nginx
```
通过ps命令得到nginx的master进程id为352，通过hup信号重启配置
```
kill -HUP 352
```
打开浏览器不断观察发现使用信号HUP后会自动的跳转到a.html，我们并没有重启，而且发现不是立马的跳转是过几秒后跳转的，这就是优雅的重新读取nginx的配置文件，从容的关闭旧的进程。

#### USR1重读日志
```

server{
        listen 80;
        server_name localhost;
        root /Users/lidong/www;
        index  index.html index.htm;
        access_log /Users/lidong/wwwlogs/access.log;
        error_log /Users/lidong/wwwlogs/error.log;
}

```
##### 刷新http://localhost/index.html 页面
```

查看日志情况
QiongdeMacBook-Pro:wwwlogs lidong$ ls -l
total 80
-rw-r--r--  1 lidong  staff  16201  3 30 14:36 access.log
QiongdeMacBook-Pro:wwwlogs lidong$ mv access.log access.log.bak
QiongdeMacBook-Pro:wwwlogs lidong$ ls -l
total 88
-rw-r--r--  1 lidong  staff  16410  3 30 14:42 access.log.bak
```
从上面可以看出来虽然改变了log日志文件的名称，但是log日志还是在写入，出现这问题的原因linux中文件识别是以文件node的id来的。
##### 使用USR1信号用再次刷新
```
QiongdeMacBook-Pro:wwwlogs lidong$ kill -USR1 352
QiongdeMacBook-Pro:wwwlogs lidong$ ls -l
total 88
-rw-r--r--  1 lidong  staff      0  3 30 14:49 access.log
-rw-r--r--  1 lidong  staff  16410  3 30 14:42 access.log.bak
-rw-r--r--  1 lidong  staff    252  3 30 14:28 error.log
QiongdeMacBook-Pro:wwwlogs lidong$ ls -l
total 96
-rw-r--r--  1 lidong  staff    418  3 30 14:49 access.log
-rw-r--r--  1 lidong  staff  16410  3 30 14:42 access.log.bak
-rw-r--r--  1 lidong  staff    252  3 30 14:28 error.log
```
通过USR1型号量来重读日志，继续刷新页面，会重新生成access.log日志文件，这个对于运维做日志的备份十分有作用。
这里有个小技巧，通过ps获取pid可以重新加载配置文件，平滑重启服务，但是感觉比较麻烦，我们可以使用如下方法操作.
查看配置文件知道nginx的pid存储在那个文件
```
kill -HUP `cat /usr/local/etc/nginx/nginx.pid`
```

#### USR2平滑升级
假设我们重新编译了新的版本的nginx，这个时候/usr/local/nginx/bin nginx 的版本就不是之前的版本了如果启动更新会报错。
```
kill -USR2 `cat /usr/local/etc/nginx/nginx.pid`
```
这个时候使用这个命令来平滑升级nginx服务器


