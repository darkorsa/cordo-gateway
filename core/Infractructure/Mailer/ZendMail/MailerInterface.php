<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Infractructure\Mailer\ZendMail;

use Laminas\Mail\Message;

interface MailerInterface
{
    public function send(Message $message): void;
}
