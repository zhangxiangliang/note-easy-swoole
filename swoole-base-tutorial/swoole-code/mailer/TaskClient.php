<?php

class TaskClient
{
    private $client;
    const EVENT_TYPE_SEND_MAIL = 'send-mail';

    public function __construct()
    {
        $this->client = new Swoole\Client(SWOOLE_SOCK_TCP);
        if (!$this->client->connect('127.0.0.1', 9501)) {
            $msg = 'swoole client connect failed.';
            throw new \Exception("Error : {$msg}");
        }
    }

    public function sendData($data)
    {
        $data = $this->pack($data);
        $this->client->send($data);
    }

    public function pack($data)
    {
        if(!is_array($data)) throw new \Exception("Error : data is not array.");
        return json_encode($data) . "\r\n";
    }
}
