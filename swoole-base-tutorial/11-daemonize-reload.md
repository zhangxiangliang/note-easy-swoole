# 守护进程、信号和平滑重启

## 守护进程
* 运行 server 的终端关闭了，server 也就不复存在。
* 守护进程 是一种长期生存的进程，不受终端控制，可以在后端运行。
* Swoole 配置 `daemonize` 和 `log_file` 选项，可以开启守护进程和日志记录。


## 信号
* 如果直接 kill 掉正在运行的 Swoole 进程可能导致 进程任务未完成。
* 可以发送 信号 到 Swoole 进程来完成 热重启。
* Swoole 中可以使用的信号量：
    * SIGTERM，一种优雅的终止型号，会等进程执行完成后再终止程序。
    * SIGUSR1，平稳重启所有的 Worker 进程。
    * SIGUSR2，平稳重启所有的 TaskWorker 进程。
* Swoole 会在接收到 SIGUSR1 或者 SIGUSR2 信号时
    * 等待 Worker 进程运行结束。
    * 关闭 Worker 进程。
    * 重新加载 Worker 代码并重启。
    * 只有在 onWorkerStart 回调之后加载的文件，重启才有意义。
