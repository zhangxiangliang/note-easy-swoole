# Worker 进程

## Swoole 进程模型
* 进程创建：
    * 首先创建 `Master` 进程。
        * `Master` 进程会创建 `Reactor` 线程 和 `Timer` 线程。
        * `Reactor` 线程实际运行 `epoll` 实例，用于accept客户端连接以及接收客户端数据。
    * `Master` 进程创建 `Manager` 进程。
    * `Manager` 进程为管理进程，用于创建、管理所有的Worker进程和TaskWorker进程。
        * `Worker` 经常为 `Swoole` 的工作进程，所有业务代码在此进程上运行。
        * `Reactor` 进程接收到客户端数据后，将数据打包发送给某个 `Worker` 进程。

* `Worker` 进程的生命周期：
    * `Worker` 进程被创建时，会调用 `onWorkerStart` 回调，随后进入时间循环等待数据。
    * 通过回调函数接收到数据后，开始处理数据。
    * 处理数据过程中出现错误导致进程退出，或者Worker进程处理的请求达到指定上限，调用 `onWorkerStop` 回调并结束进程。
