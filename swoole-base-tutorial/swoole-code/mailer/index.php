<?php

require_once "./TaskClient.php";

$data = [
    'event' => TaskClient::EVENT_TYPE_SEND_MAIL,
    'to' => '326277403@qq.com',
    'subject' => 'just a test',
    'content' => 'This just a test.',
];

$client = new TaskClient();
$client->sendData($data);
