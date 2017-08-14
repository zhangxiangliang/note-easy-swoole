# task 初体验

## 简介
* AsyncTask 异步任务，将一个耗时的任务投递到队列中，由进程池异步去执行。
* 如果没使用异步 php 线程一直被阻塞，客户端需要一直等待服务端响应。
* 无异步处理的情景：
    * 用户发送10封邮件需要等待10封邮件都发完。
    * 批量导入数据，需要等待数据都导入完成。

## 使用
##### 创建 server
```
$serv = new swoole_server("127.0.0.1", 9501);
```

##### 开启 task 功能
* task 进程是在 worker 进程内发起的，并投递到 task 进程中。
* `swoole_serve->task()` 是非阻塞的，但是 task 进程是阻塞的，得排队等待。
* 投递的任务大于 task 进程能力，就会阻塞缓冲区，导致 worker 进程阻塞。
* task 进程中使用 return 或 `swoole_server->finish()` 就会通知 worker 进程任务已经完成， worker 进程将会继续调用 onFinish 回调。
* task 功能默认关闭，开启条件：
    * 设置 task_worker_num 的数量。
    * 注册 task 的回调函数 onTask 和 onFinish。

```
$serv->set(['task_worker_num' => 1]);
```

##### 使用总结
* 没有耗时任务的情况下，worker 直接运行，无需开启 task。
* 对于耗时的任务，可以在 worker 中调用 task 函数，来投递任务。
* task 进程内使用选择调用 `finish()` 或者使用 `return` 通知任务完成。
* worker 进程不关心任务的结果，finish就不需要了。
