# 粘包问题

## 问题
* client/server 数据传递的过程：
    * 客户端 -> 发送数据
    * 服务端 -> 接受数据
    * 每个 TCP socket 都有一个 发送缓冲区 和 接受缓冲区。
    * `client->send()` 负责通知 socket 存储数据到 buffer 区。
    * `TCP` 负责将 buffer 区的数据发送给服务器。
* onReceive 中没办法保证数据包的完整性：
    * 可能收到多个请求包。
    * 也可能收到一个请求包的一部分数据。

## 解决
* EOF 结束协议
    * 在每个数据包结尾加上 `EOF` 标记。
    * 开启 `open_eof_check => true`。
    * 指定 eof 标记 `package_eof => "\r\n"`。
    * 需要自行拆包，也可以使用 `open_eof_split` 自动拆包，但是性能差。
    * `open_eof_split` 没开启的话，其实也还是粘包。
    * ！！！必须都带上 EOF ！！！


* 固定包头 + 包体协议
    * 固定包头是一种通用的协议，在发送的数据包前添加一段信息，长度2个字节或4个字节。
    * 使用 `pack()` 函数来把数据打包成二进制字符串。
    * Server 收到一个数据包，会先解除包的长度，然后去读取相应长度的数据，依次循环。
    * 设置参数：
        * `open_length_check => true`  开启协议解析。
        * `package_length_type => 'N'` 长度字段的类型,表示4个字节。
        * `package_length_offset => 0` 第几个字节是包的长度值。
        * `package_body_offset => 4` 第几个字节开始计算长度，整数一般4个字节。
        * `package_max_length => 81920` 协议最大长度。

```
// Server
$info = unpack('N', $data);
$len = $info[1];
$body = substr($data, - $len);

// Client
$data = "Just a test.";
$data = pack('N', strlen($data)) . $data;
$client->send($data);
```
