<?php

class CommentServer
{
    private $serv;
    private $key = 'taroball.net';
    private $fd;
    private $users = [];
    private $config = [
        'worker_num' => 1,
        'heartbeat_check_interval' => 60,
        'heartbeat_idle_time' => 125
    ];

    public function __construct()
    {
        $this->serv = new Swoole\WebSocket\Server('192.168.1.125', 9501);
        $this->serv->set($this->config);
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
        return $this->callMethodByFrameAndData($method, $frame, $data);
    }

    public function onClose($serv, $fd)
    {
        echo "client {$fd} closed.\n";
    }

    public function callMethodByFrameAndData($method, $frame, $data)
    {
        if(!method_exists($this, $method)) {
            $this->close($frame->fd, 'event is not exits.');
            return false;
        }
        $this->$method($frame->fd, $data);
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
