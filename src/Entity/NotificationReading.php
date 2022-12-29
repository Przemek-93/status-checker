<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReadingRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Exception;

#[ORM\Entity(repositoryClass: ReadingRepository::class)]
class NotificationReading
{
    public const READING_STATUSES_OK = [
        Response::HTTP_OK,
        Response::HTTP_CREATED,
        Response::HTTP_ACCEPTED,
        Response::HTTP_NO_CONTENT,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(type: 'int')]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'int')]
    private int $status;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'array')]
    private array $body;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Type(type: DateTime::class)]
    private DateTime $readAt;

    #[ORM\ManyToOne(inversedBy: 'readings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Notification $notification = null;

    public function __construct(
        int $status,
        DateTime $readAt,
        array $body = [],
    ) {
        $this->status = $status;
        $this->readAt = $readAt;
        $this->body = $body;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getReadAt(): ?DateTime
    {
        return $this->readAt;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    public function getNotificationId(): ?int
    {
        if ($this->notification) {
            return $this->notification->getId();
        }

        throw new Exception('Missed notification.');
    }

    public function isFailed(): bool
    {
        if ($this->status) {
            if (in_array($this->status, self::READING_STATUSES_OK)) {
                return false;
            }

            return true;
        }

        throw new Exception('Missed status.');
    }
}
