<?php
require_once __DIR__ . '/vendor/autoload.php';

class Mailer
{
    public $transport;
    public $mailer;

    public function send($data)
    {
        $this->initTransport();
        $this->initMailer();
        $result = $this->sendMailer($this->initMessage($data));
        $this->destroy();
        return $result;
    }

    public function initTransport()
    {
        $this->transport = (new Swift_SmtpTransport('smtp.qq.com', 25, 'tls'))
            ->setUsername('')
            ->setPassword('');
    }

    public function initMailer()
    {
        $this->mailer = new Swift_Mailer($this->transport);
    }

    public function initMessage($data)
    {
        return (new Swift_Message($data['subject']))
            ->setFrom(['admin@taroball.net' => '芋圆网'])
            ->setTo($data['to'])
            ->setBody($data['content']);
    }

    public function sendMailer($message)
    {
        return $this->mailer->send($message);
    }

    public function destroy()
    {
        $this->transport = null;
        $this->mailer = null;
    }
}
