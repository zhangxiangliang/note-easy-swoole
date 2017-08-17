# websocket 常见问题

## 心跳检测
* Master 进程包括 主线程，多个 Reactor 线程，心跳检查功能。
* 心跳检查用于定时检测客户端是否还链接。
* 心跳检查可以预防 C/S 连接不稳定，防止重复建立连接，大量 fd 被浪费。
* 可以使用 定时器来实现，但 swoole 已经自带了该功能。
* 配置项
    * `heartbeat_check_interval => n`。
    * `heartbeat_idle_time => m`。
    * 两者需要配合使用，表示 n 秒检查一次哪些连接 m 秒内没有活动。

## 校验客户端连接的有效性
* 当部署生产环境 server 监听为 0.0.0.0 或者 本机ip。
* 这将导致所有的人都可以连接我们的 swoole 服务器。
* 可以使用携带 token 方法来解决：
    * 当握手完成后，验证用户 token。
    * 如果通过则，继续请求。
    * 如果不通过则，关闭连接。
    * 代码 [传送门](./swoole-code/swoole-valid-token/)

## 客户端重连机制
* client 当 server 连接断开时，重新主动连接 server。
* 该机制也称作 `双向心跳` 或者 `保活机制`。
* 代码 [传送门](./swoole-code/client-heartbeat/)
