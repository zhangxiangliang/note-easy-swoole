# websocket 初识

## 什么是 websocket
* websocket 和 socket 的关系是 javascript 和 java 的关系。
* websocket 是一个协议，和 http, https, ftp 协议一样。

* 对比 http 协议
    * http 是非持久连接。
    * webscoket 是持久连接。
    * http 要实现双向通信 只能使用 轮询 和 long pull，需要不断建立连接，非常消耗带宽和服务器资源。
    * websocket 是一种独立的，基于 TCP 协议，是双向持久连接，只需要第一次建立连接就可以实现双向通信。
    * websocket 适合做通信、推送相关的服务。

## 创建 websocket 服务器
* 使用 swoole_websocket_server 对象创建。
* 回调方法：
    * open 当建立连接调用 `function open($serv, $request)`。
    * message 接受消息后调用 `function message($serv, $frame)`。
    * close 当连接关闭调用 `function close($serv, $fd)`。
* 成员方法：
    * push 用于向客户端发送消息 `function push($fd, $content)`。

## websocket C/S 实例
* 代码 [传送门](./swoole-code/websocket/)。
