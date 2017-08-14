# 定时器

## 引子
* javascript 提供 setInterval 和 setTimeout 两个定时器函数。
* 后台开发中，有时候也需要定时器：数据库备份、排行榜更新。
* 可以使用 linux 的 crontab 工具。
* Swoole 也提供 `永久性定时器` 和 `一次性定时器`。

## 永久性定时器
* `int swoole_timer_tick(int $ms, callable $callable, mixed $params)`。
* `swoole_server->tick()`。
* $ms 指时间，单位毫秒。
* $callable 回调函数。
* $params 传递给回到函数的参数。
* 函数是全局性的在哪都可以使用。

## 一次性定时器
* `int swoole_timer_after(int $ms, callable $callable, mixed $params)`。
* `swoole_server->after()`。

## 清除定时器
* `bool swoole_timer_clear(int $timerId)`。
* `swoole_server->clearTimer()`。


