# Linux IO 模式

## IO 模式
### 阻塞 I/O
* blocking IO
* kernel IO 准备数据、等待网络数据时 Process 会被阻塞。
* 当 kernel 拷贝完数据到用户内存后，进程接触 Block。
* IO 执行中的两个阶段都被 block 了。

### 非阻塞 I/O
* nonblocking IO
* 用户进程会不断的主动询问 kernel 数据好了没有。

### I/O 多路复用
* IO multiplexing / Event driven IO
* 使用  select / poll / epoll 来完成。
* 当用户调用了 select 进程将会被 blocking。
* kernel 会监视所有 select 负责的 socket。
* 当任何一个 socket 中的数据准备好了，select 会返回，process 会调用 read，拷贝数据到用户进程。
* 特点是通过一种机制可以让一个进程调用和等待多个文件描述符。
* 与 blocking IO 相似，连接处理数上更胜一筹。

### 异步 I/O
* asynchronous IO
* 用户进程发起 read 操作后，就可以去进行别的事情。
* kernel 等待数据准备完成，将数据拷贝到用户内存后，会发送一个 signal，用户进程再继续执行 read 操作。
