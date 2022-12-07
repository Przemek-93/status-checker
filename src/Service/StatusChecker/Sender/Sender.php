<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Sender;

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
        protected string $addressFrom,
        protected Environment $templating,
        protected LoggerInterface $logger
    ) {
    }

    public function send(
        array $context,
        string $template,
        string $receiver,
        string $subject,
        ?string $addressFrom = null
    ): void {
        try {
            $this->mailer->send(
                (new Email())
                    ->from(new Address($addressFrom ?? $this->addressFrom))
                    ->to($receiver)
                    ->subject($subject)
                    ->html(
                        $this->templating->render(
                            $template,
                            $context
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
