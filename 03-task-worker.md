# Task Worker

## 简介
* Task Worker 是 Swoole 中的一种特殊的工作进程。
* 该进程作用是用来处理一些比较耗时较长的任务，来释放 Worker 进程。
* Worker 进程可以通过 `swoole_server` 对象的 `task()` 来投递任务到 Task Worker。
* Worker 进程通过 Unix Sock 管道将数据发送给 TaskWorker，Worker 可以继续处理新逻辑，无需等待任务完成。
* Task Worker 是一个独立进程，因此无法在两个进程之间共享变量，需要使用 swoole_table 或者 redis 等工具。

## 实例
##### 设置 swoole_server 的配置：
```
// 设置启动 2 个 task 进程。
$serv->set(['task_worker_num' => 2]);
```

##### 绑定必要的回调函数
```
$serv->on('Task', 'onTask');
$serv->on('Finish', 'onFinish');
```

##### 回调函数的原型。
```
function onTask(swoole_server $serv, $task_id, $from_id, $data);
function onFinish(swoole_server $serv, $task_id, $data);
```

##### 发起任务请求
```
// -1 代表不指定 task 进程
$serv->task("task data", -1);

// 1.8.6+ 可以动态指定 onFinish 函数
$serv->task("task data", -1, function ($serv, $task_id, $data) {
    echo "Task Finish Callback\n";
});
```
