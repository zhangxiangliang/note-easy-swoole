<?php

class WebSocketServerValid
{
    private $serv;
    private $key = 'taroball.net';
    private $conf = [
        'worker_num' => 1,
        'heartbeat_check_interval' => 30,
        'heartbeat_idle_time' => 62,
    ];

    public function __construct()
    {
        $this->serv = new Swoole\WebSocket\Server('192.168.1.125', 9501);
        $this->serv->set($this->conf);
        $this->load(['Open', 'Close', 'Message']);
    }

    public function load($items)
    {
        foreach ($items as $item) $this->serv->on($item, [$this, 'on' . $item]);
    }

    public function onOpen($serv, $request)
    {
        if($this->checkAccess($serv, $request)) {
            echo "server: handshake success with fd{$request->fd}";
        } else {
            $this->serv->close($request->fd);
        }
    }

    public function onMessage($serv, $frame)
    {
        $this->serv->push($frame->fd, 'Server: ' . $frame->data);
    }

    public function onClose($serv, $fd)
    {
        echo "client {$fd} closed.\n";
    }

    public function checkAccess($serv, $request)
    {
        if(!isset($request->get) || !isset($request->get['uid']) || !isset($request->get['token'])) {
            return false;
        }

        $uid = $request->get['uid'];
        $token = $request->get['token'];
        return md5(md5($uid) . $this->key) == $token ? true : false;
    }

    public function start()
    {
        $this->serv->start();
    }
}

$server = new WebSocketServerValid;
$server->start();
