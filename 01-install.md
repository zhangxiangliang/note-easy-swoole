# 安装

## 安装源
* 源地址 [传送门](https://ius.io/GettingStarted/)

## 命令集合
* 安装 rpm 包 `rpm -Uvh [url]`。
* 解压 tar.gz 文件 `tar zxf [filepath]`。
* 下载文件 `wget [url]`。

## 安装PHP7
##### 下载地址
* PHP 7 `http://cn2.php.net/distributions/php-7.0.2.tar.gz`。

##### 编译配置
```
./configure
--prefix=/user/local/php \
--with-config-file-path=/etc/php \
--enable-fpm \
--enable-pcntl \
--enable-mysqlnd \
--enable-opcache \
--enable-sockets \
--enable-sysvmsg \
--enable-sysvsem \
--enable-sysvshm \
--enable-shmop \
--enable-zip \
--enable-soap \
--enable-xml \
--enable-mbstring \
--disable-rpath \
--disable-debug \
--disable-fileinfo \
--with-mysql=mysqlnd \
--with-mysqli=mysqlnd \
--with-pdo-mysql=mysqlnd \
--with-pcre-regex \
--with-iconv \
--with-zlib \
--with-mcrypt \
--with-gd \
--with-openssl \
--with-mhash \
--with-xmlrpc \
--with-curl \
--with-imap-ssl
```

##### 编译命令
```
sudo make
sudo make install
```

##### 配置文件
```
sudo mkdir /etc/php
sudo cp php.ini-development /etc/php/php.ini
```

##### 添加环境变量
```
vi ~/.bashrc

export PATH=/usr/local/php/bin:$PATH
export PATH=/usr/local/php/sbin:$PATH

source ~/.bashrc
```

## 安装 swoole
* 下载地址 `https://github.com/swoole/swoole-src/releases`。
* 查看拓展是否安装 `php -m`。

##### 编译与配置
```
phpize
./configure
sudo make
sudo make install
```

##### 添加扩展
```
vi /etc/php/php.ini

extension=swoole.so
```
