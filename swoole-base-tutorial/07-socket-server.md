# 异步多线程服务器

## 异步与同步
* web 开发模式下，我们碰到的基本上都是同步的。
* 无论 fpm 还是 httpd，同一时间内进程只能处理一个请求。
    * 可以通过调节 worker 进程的数量。
    * 系统资源也会逐渐消耗光。
* swoole 既支持异步，也支持同步。

## socket 编程
##### socket 是什么？
* 用来与另一个进程进行跨网络通信的文件。
* socket 也可以理解为一组函数。
* Client-Server 模型中就是利用 socket 进行通信。
    * Server 创建一个 socket 绑定 ip 和 port。
    * Server 进行 listen 并使用 accept 函数阻塞。
    * Client 连接服务器。
    * Server 接受 Client 数据并处理数据。
    * Server 返回数据给 Client。
    * Server 关闭客户端，并重新使用 accept 函数阻塞。

## 初识 server
##### 创建 server 对象。
```
$server = new swoole_server('127.0.0.1', 9501);
```

##### 设置 worker 进程
* worker_num 最好是 CPU 核数的 1-4 倍。
* 进程开启越多，内存占用越多，进程之间切换的资源消耗更多。
* worker_num 默认是 CUP 的核数。
```
$server->set(['worker_num' => 2]);
```

##### 设置事件
* swoole_server 是事件驱动的。
* 不需要关注底层，只需要对底层相应的动作注册相应的回调。
* 在回调中实现业务逻辑。
* `Connect` 当新 Client 连接时触发。
* `Receive` 当收到 Client 数据触发。
* `Close` 当 Client 断开连接，或者是 Server 主动关闭连接。
* 参数解释
    * $serv 是 swoole_server 对象。
    * $fd 是唯一标识，用于区分客户端，范围（1~16万）。
    * $fromId 是指的哪一个 reactor 线程。
    * $data 是服务端接受到的数据，注意是字符串或者二进制内容。

```
$server->on('Connect', function ($serv, $fd) {
    echo "new client connected." . PHP_EOL;
});

$server->on('Receive', function ($serv, $fd, $fromId, $data) {
    $serv->send($fd, 'Server ' . $data);
});

$server->on('Close', function ($serv, $fd) {
    echo "Client close." . PHP_EOL;
});
```

##### 设置客户端
```
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
$client->connect('127.0.0.1', 9501) || exit("connect failed. Error: {$client->errCode}\n");
$client->send("hello server.");
$response = $client->recv();
echo $response . PHP_EOL;
$client->close();
```
