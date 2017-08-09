# Timer 定时器

## 定时器原理
* Swoole 扩展提供的一个毫秒级定时器。
* 每隔指定的时间间隔之后执行一次指定的回调函数，实现定时任务的功能。
* 新版本 Swoole 定时器基于 epoll 方法的 timeout 机制实现，不依赖单独的定时线程。
* 拓展使用最小堆存储定时器，减小定时器的检索次数，提高了运行效率。

## 定时器使用
```
// function onTimer(int $timer_id, mixed $params = null);
int swoole_timer_tick(int $ms, mixed $callback, mixed $param = null);
int swoole_server::tick(int $ms, mixed $callback, mixed $param = null);

// function onTimer();
void swoole_timer_after(int $after_timer_ms, mixed $callback_function);
void swoole_server::after(int $after_timer_ms, mixed $callback_function);
```

## tick 永久定时器
* 使用 tick 方法创建的定时器会一直运行，指定时间会执行一个 callback 函数。
* tick 定时器会返回定时器的 id，当不需要定时器的时候可以利用 `swoole_timer_clear(id)` 来结束。
* 创建的定时器是不能跨进程的，因此在哪个 Worker 定时器创建的，也只能在这个 Worker 进程中删除。

## after 临时定时器
* 使用 after 创建的定时器执行结束后，就会自动删除。
* after 定时器的回调不接受任何参数，可以通过闭包方式传递参数，或者使用成员变量。
