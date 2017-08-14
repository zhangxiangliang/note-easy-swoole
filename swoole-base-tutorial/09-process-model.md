# 进程模型

## Master-Manager-Worker
* Master 进程是一个多线程程序。
    * Master 是 Swoole 的主进程。
    * 权限处于最高级，如果 Master 进程挂起，则整个程序崩溃。
    * Master 进程中包括 主线程 和 多个 Reactor 线程等。
        * 主线程用于 Accept、信号处理等操作。
        * Reactor 线程用于处理 tcp连接、处理网络IO、收发数据的线程。
        * 主线程 建立完连接，Reactor 线程会负责一直监视 socket，并将数据传递给 Worker 进程。
* Manager 进程是一个管理程序。
    * Master-Worker 模型中，Worker 是 Master 进程复制出来的。
    * 父进程可以通过 fork() 创建一个新的子进程。
    * fork() 操作是不安全的，在 swoole 中使用专门的 Manager 来管理。
    * Manager 进程专门负责 `worker/task` 进程的 `fork` 操作和管理。
        * 创建 Worker 进程 处理业务等。
        * 创建 Task 进程 异步的消耗任务处理。

## 进程简单的回调函数
* TaskWorker 进程也会触发 onWorkerStart 回调。
* `swoole_set_process_name()` 可以设置进程名。
* $workerId 的分配 [0, worker_num) 是 Worker 进程。
* $workerId 的分配 [worker_num, worker_num + task_worker_num) 是 TaskWorker 进程。
* `$serv->setting['key']` 可以用来获取配置的 server 信息。

```
Master 进程：
    启动：onStart
    关闭：onShutdown
Manager 进程：
    启动：onManagerStart
    关闭：onManagerStop
Worker 进程：
    启动：onWorkerStart
    关闭：onWorkerStop
```

```
$serv->on("Start", function ($serv){
    swoole_set_process_name('server-process: master');
});
$serv->on('ManagerStart', function ($serv){
    swoole_set_process_name('server-process: manager');
});
$serv->on('WorkerStart', function ($serv, $workerId){
    if($workerId >= $serv->setting['worker_num']) {
        swoole_set_process_name("server-process: task");
    } else {
        swoole_set_process_name("server-process: worker");
    }
});
```
