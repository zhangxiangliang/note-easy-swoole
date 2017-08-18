<?php

use Exception;

class AdminClient
{
    private $client;
    private $ip = '192.168.1.125';

    public function __construct()
    {
        $this->client = new Swoole\Client(Swoole_SOCK_TCP);
        $this->client->connect($this->ip, 9502)
            || throw new Exception("Error: swoole client connect failed.");
    }

    public function sendData($data)
    {
        $data = $this->formatJSON($data);
        $this->client->send($data);
    }

    public function formatJSON($data)
    {
        return is_array($data) ? json_encode($data) . "\r\n" : false;
    }
}

$client = new Client;
$client->sendData(['event' => 'alertTip', 'toUid' => 100]);
