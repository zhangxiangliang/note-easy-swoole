<?php
class TaskServer
{
    private $_serv;
    private $_run;
    private $_conf = [
        'worker_num' => 2,
        'daemonize' => false,
        'log_file' => __DIR__ . '/server.log',
        'task_worker_num' => 2,
        'max_request' => 5000,
        'task_max_request' => 5000,
        'open_eof_check' => true,
        'package_eof' => "\r\n",
        'open_eof_split' => true,
    ];

    public function __construct()
    {
        $this->_serv = new Swoole\Server('127.0.0.1', 9501);
        $this->_serv->set($this->_conf);
        $this->_serv->on('Connect', [$this, 'onConnect']);
        $this->_serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->_serv->on('Receive', [$this, 'onReceive']);
        $this->_serv->on('Task', [$this, 'onTask']);
        $this->_serv->on('Finish', [$this, 'onFinish']);
        $this->_serv->on('Close', [$this, 'onClose']);
    }

    public function onConnect($serv, $fd, $fromId) {}

    public function onWorkerStart($serv, $workerId)
    {
        require_once __DIR__ . '/TaskRun.php';
        $this->_run = new TaskRun;
    }

    public function onReceive($serv, $fd, $fromId, $data)
    {
        $data = $this->unpack($data);
        $this->_run->receive(...func_get_args());
        empty($data['event']) || $this->_serv->task(array_merge($data , ['fd' => $fd]));
    }

    public function onTask($serv, $taskId, $fromId, $data)
    {
        $this->_run->task(...func_get_args());
    }

    public function onFinish($serv, $taskId, $fromId)
    {
        $this->_run->finish(...func_get_args());
    }

    public function onClose($serv, $fd, $fromId) {}

    public function unpack($data)
    {
        $data = str_replace("\r\n", '', $data);
        $data = !$data ? false : json_decode($data, true);
        $data = $data && is_array($data) ? $data : false;
        return $data;
    }

    public function start()
    {
        $this->_serv->start();
    }
}

$reload = new TaskServer;
$reload->start();
