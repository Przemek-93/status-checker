<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Sender;

use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Throwable;

class Sender
{
    public function __construct(
        protected MailerInterface $mailer,
        protected string $sentFrom,
        protected Environment $templating,
        protected LoggerInterface $logger
    ) {
    }

    public function send(array $sendDataArray): void
    {
        foreach ($sendDataArray as $receiver => $array) {
            $isFailed = false;
            foreach ($array as $sendData) {
                if ($sendData->isReadingFailed()) {
                    $isFailed = true;
                }
            }

            if ($isFailed) {
                try {
                    $this->mailer->send(
                        (new Email())
                            ->from(new Address($this->addressFrom))
                            ->to($receiver)
                            ->subject('Status-checker notification ' . (new DateTime())->format('Y-m-d H:i:s'))
                            ->html(
                                $this->templating->render(
                                    '',
                                    $sendDataArray
                                )
                            )
                    );
                } catch (Throwable $throwable) {
                    $this->logger->error(
                        sprintf(
                            'Something went wrong while trying to send notification to %s, error: %s',
                            $receiver,
                            $throwable->getMessage()
                        )
                    );
                }
            }
        }
    }
}
