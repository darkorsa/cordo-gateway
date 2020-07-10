<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Application\Error\Handler;

use Throwable;
use Laminas\Mail\Message;
use Cordo\Gateway\Core\Application\Error\ErrorHandlerInterface;
use Cordo\Gateway\Core\Infractructure\Mailer\ZendMail\MailerInterface;

class EmailErrorHandler implements ErrorHandlerInterface
{
    private $mailer;

    private $from;

    private $to;

    public function __construct(MailerInterface $mailer, string $from, array $to)
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->to = $to;
    }

    public function handle(Throwable $exception): void
    {
        $messageText = sprintf(
            'Exception occured in file %s on line %d with message: %s',
            $exception->getFile(),
            $exception->getLine(),
            $exception->getMessage()
        );

        $message = new Message();
        foreach ($this->to as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message->addTo($email);
            }
        }
        $message->addFrom($this->from)
            ->setSubject('Critical Error')
            ->setBody($messageText);

        $this->mailer->send($message);
    }
}
