# 多端口监听

## 多端口监听
* 应用服务器可能既需要监听外网的服务端口，也需要监听内网的管理端口。
* Swoole 提供 `addlistener` 函数用于给服务器添加一个需要监听的 host 和 port。
* 使用 `addlistener` 要在 `start` 前。
* 两个端口的数据都会在同一 `onReceive` 中被获取到，可以使用 `connection_info`来获取 fd。

##### 简单示例
```
$serv = new swoole_server("192.168.1.1", 9501);
$serv->addlistener("127.0.0.1", 9502, SWOOLE_TCP);
$ser->start();
```

##### 获取 port 示例
```
$info = $serv->connection_info($fd, $from_id);
switch(info['from_port']) {
    case 9502: $serv->send($fd, "welcome admin\n"); break;
    default: $serv->send($fd, 'Swoole: ' . $data);
}
```

## 多端口混合协议监听
Swoole 可以监听多个端口，每个端口都可以设置不同的协议处理方法和回调函数。
```
$port1 = $server->listen("127.0.0.1", 9501, SWOOLE_SOCK_TCP);
$port2 = $server->listen("127.0.0.1", 9502, SWOOLE_SOCK_UDP);
$port3 = $server->listen("127.0.0.1", 9503, SWOOLE_SOCK_TCP | SWOOLE_SSL);
```

Swoole 还可以为每个 port 对象分别设置配置选项。
```
$port1->set(['open_length_check' => true]);
```

Swoole 还可以为每个 port 对象分比设置自己独有的回调函数。
```
$port1->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Swoole: '.$data);
    $serv->close($fd);
});
```

### 注意事项
* 未设置协议处理选项的监听端口，默认使用无协议模式。
* 未设置回调函数的监听端口，使用$server对象的回调函数。
* 监听端口返回的对象类型为swoole_server_port。
* 不同监听端口的回调函数，仍然是相同的Worker进程空间内执行。
* 主服务器是WebSocket或Http协议，新监听的TCP端口默认会继承主Server的协议设置，必须单独使用 `set` 方法设置新的协议才会启用新协议。

