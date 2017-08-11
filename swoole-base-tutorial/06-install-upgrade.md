# Swoole 的安装与升级

## 简介
* Swoole是PHP语言的高性能网络通信框架。
* 与其他扩展不同，swoole运行后会接管 PHP 的控制权，进入事件循环。
* 当IO事件发生后，swoole 会自动回调指定的 PHP 函数。
* 提供了PHP语言
    * 异步多线程服务器。
    * 异步 TCP/UDP 客户端。
    * 异步 MySQL。
    * 数据库连接池。
    * AsyncTask。
    * 消息队列。
    * 毫秒定时器。
    * 异步文件读写。

## 准备
##### 需要的基础软件
```
php (5.4+)
make
autoconf
gcc (4.4+)
```

##### 下载源文件
```
wget http://pecl.php.net/get/swoole-1.9.6.tgz
tar zxvf swoole-1.9.6.tgz
cd swoole-1.9.6
```

##### 编译安装
在不确保终端下的phpize是哪个版本的php时候，建议指定绝对路径
```
phpize
./configure
make
sudo make install
```

##### 添加配置项
```
vi php.ini
extension=swoole.so
```

##### 查看模块是否被安装
```
php -m | grep swoole
```

## 升级
* 直接安装需要的版本即可。
