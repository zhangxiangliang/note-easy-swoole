<?php
require_once './TaskClient.php';
require_once './Mailer.php';

class TaskRun
{
    public function receive($serv, $fd, $fromId, $data) {}

    public function task($serv, $taskId, $fromId, $data)
    {
        try {
            switch ($data['event']) {
                case TaskClient::EVENT_TYPE_SEND_MAIL: $result = (new Mailer)->send($data); break;
                default: break;
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('task exception : '. $e->getMessage());
        }
    }

    public function finish($serv, $taskId, $data)
    {
        return true;
    }
}
