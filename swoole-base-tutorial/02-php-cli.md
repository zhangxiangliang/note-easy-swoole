# php 的 cli 模式
* php 常见的开发模式都是基于 web，借助 nginx 或者 apache 搭建服务器。
* nginx 可以用 php-fpm 模块来解析 php 脚本。
* apache 可以用其他模块来解析 php 脚本。
* php 还有 CLI (Command Line Interface) 模式。

## 常见用法
* 查看 php 版本 `php -v` (version)。
* 检查 php 模块 `php -m` (model)。
* 查看 phpinfo 信息 `php -i` (info)。
* 过滤信息 `php -i | grep php.ini` (info)。
* 运行 php 文件 `php filename.php` (file)。
* 检查 php 语法错误 `php -l filename.php` (lint)。
* 设置 php.ini 文件 `php -c /path/file.ini` (config)。
* 运行 php 代码 `php -r "echo 'hello world'"` (run)。
