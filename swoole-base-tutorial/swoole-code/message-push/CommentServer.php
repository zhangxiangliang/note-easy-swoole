<?php

use Exception;

class CommentServer
{

    private $key = 'taroball.net';
    private $users = [];

    private $serv;
    private $tcpServ;
    private $ip = '192.168.1.125';
    private $port = 9501;
    private $tcpPort = 9502;
    private $config = [
        'worker_num' => 1,
        'heartbeat_check_interval' => 60,
        'heartbeat_idle_time' => 125,
    ];
    private $tcpConfig = [
        'open_eof_check' => true,
        'package_eof' => "\r\n",
        'open_eof_split' => true,
    ];

    public function __construct()
    {
        $this->serv = new Swoole\WebSocket\Server($this->ip, $this->port);
        $this->tcpServ = $this->serv->listen($this->ip, $this->tcpPort);

        $this->serv->set($this->config);
        $this->tcpServ->set($this->tcpConfig);

        $this->tcpConfig->on('Receive', [$this, 'onReceive']);
        $this->load(['Open', 'Close', 'Message']);
    }

    public function load($items)
    {
        foreach ($items as $item) $this->serv->on($item, [$this, 'on' . $item]);
    }

    public function onOpen($serv, $request)
    {
        if(!$this->checkAccess($request)) return false;

        $uid = $request->get['uid'];
        $fd = $request->fd;
        return $this->setUsersByUidAndFd($uid, $fd);
    }

    public function onMessage($serv,$frame)
    {
        $data = $frame->data;
        $data = json_decode($data, true);
        if(!$data || !is_array($data) || !isset($data['event'])) {
            $this->close($frame->fd, 'data format invalidate');
            return false;
        }

        $method = $data['event'];
        return $this->callMethodByFrameAndData($method, $frame->fd, $data);
    }

    public function onClose($serv, $fd)
    {
        echo "client {$fd} closed.\n";
    }

    public function onReceive($serv, $fd, $fromId, $data)
    {
        try {
            $data = json_decode($data, true);
            if(!isset($data['event'])) throw new Exception("params error, needs event params.");

            $method = $data['event'];
            if(!method_exists($this, $method)) throw new Exception("params error, not support method.");

            $this->callMethodByFrameAndData($method, $fd, $data);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function callMethodByFrameAndData($method, $fd, $data)
    {
        if(!method_exists($this, $method)) {
            $this->close($fd, 'event is not exits.');
            return false;
        }
        $this->$method($fd, $data);
        return true;
    }

    public function setUsersByUidAndFd($uid, $fd)
    {
        if(!array_key_exists($uid, $this->users)) {
            $this->users[$uid] = $fd;
            return true;
        }

        $oldFd = $this->users[$uid];
        $this->close($oldFd, 'uid exists.');
        $this->users[$uid] = $fd;
        return false;
    }

    public function checkAccess($request)
    {
        if(!isset($request->get)
            || !isset($request->get['uid'])
            || !isset($request->get['token']))
            return false;

        $uid = $request->get['uid'];
        $token = $request->get['token'];

        return md5(md5($uid) . $this->key) === $token ? true : false;
    }

    public function close($fd, $message = '')
    {
        if ($uid = array_search($fd, $this->users)) {
            unset($this->users[$uid]);
        }

        $this->serv->close($fd);
    }

    public function alertTip($fd, $data)
    {
        // 推送目标用户的uid非真或者该uid尚无保存的映射fd，关闭连接
        if (empty($data['toUid']) || !array_key_exists($data['toUid'], $this->users)) {
            $this->push($fd, ['event' => $data['event'], 'msg' => '对方不在线上.']);
            // $this->close($fd);
            return false;
        }
        $this->push($this->users[$data['toUid']], ['event' => $data['event'], 'msg' => '收到一条新的回复.']);
    }

    public function push($fd, $message)
    {
        $message = is_array($message) ? $message : [$message];
        $message = json_encode($message);
        if($this->serv->push($fd, $message) == false) $this->close($fd, 'user not online');
    }

    public function start()
    {
        $this->serv->start();
    }
}

$server = new CommentServer;
$server->start();
