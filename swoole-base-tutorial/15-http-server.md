# HTTP 服务器

## 问题
* php 可以自己创建 http 服务器。
* swoole_http_server 继承 http_server 用于创建 swoole 版 http 服务器。
* swoole_http_server 对 HTTP 协议支持还不成熟。

## 简介
* swoole_http_server 的 `onConnect` 和 `onReceive` 回调不能使用。
* swoole_http_server 的其他 `swoole_server` 提供的 API 可以使用。
* swoole_http_server 增加了 onRequest 回调。

## 使用
##### 对象和函数解析
* swoole_http_server 用来创建服务器。
* onRequest 回调参数：
    * swoole_http_request 用来接受数据。
    * swoole_http_response 用来返回数据。
* swoole_http_response 部分方法：
    * status 用来设置 http 响应状态。
    * end 用来返回 http 请求内容。
* swoole_http_request 部分参数：
    * 可以获取 header 信息。
    * 可以获取 server 信息。
    * 可以获取 get 信息。
    * 可以获取 post 信息。
    * 可以获取 files 信息。
    * 可以获取 cookie 信息。
    * 可以获取 fd 客户端标识。
    * 可以获取 data 数据。

##### 提示
* 可以利用 `swoole_http_request->server['request_uri']` 来做路由解析。
* 请求可能会出现两个，其中一个是 `/favicon.icon` 请求。

##### 简单的启动一个服务器
```
$http = new swoole_http_server('127.0.0.1', 8080);
$http->on('Request', function (swoole_http_request $request, swoole_http_response $response) {
    $response->status(200);
    $response->end('hello world.');
});
$http->status();
```

## nginx 代理
* http 请求的处理上使用 nginx.
* nginx 把请求转发给 swoole。
* (nginx-fpm 替换为 nginx-swoole)。

#### 简单例子
```
server {
    listen       80;
    root /var/www/test/;
    server_name  swoole.example.com;
    index index.php index.html;
    location / {
        if (!-e $request_filename) {
            proxy_pass http://127.0.0.1:8000;
        }
    }
}
```
