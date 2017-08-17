<?php
class WebSocketServer
{
    private $serv;

    public function __construct()
    {
        $this->serv = new Swoole\WebSocket\Server;
        $this->load(['Open', 'Message', 'Close']);
    }

    public function load($items)
    {
        foreach ($items as $item) $this->serv->on($item, [$this, 'on' . $item]);
    }

    public function onOpen($serv, $request)
    {
        echo "server: handshake success with fd{$request->fd}.\n";
    }

    public function onMessage($serv, $frame)
    {
        for ($serv->connections as $fd) {
            $this->serv->push($fd, 'userid : ' . $frame->fd . ' message : ' . $frame->data);
        }
    }

    public function onClose($serv, $fd)
    {
        echo "server: client fd{$fd} closed.\n";
    }

    public function start()
    {
        $this->serv->start();
    }
}
