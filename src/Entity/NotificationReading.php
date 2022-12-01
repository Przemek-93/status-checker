<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReadingRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Exception;

#[ORM\Entity(repositoryClass: ReadingRepository::class)]
#[ORM\HasLifecycleCallbacks]
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
    private ?int $status = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'array')]
    private array $content = [];

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Type(type: DateTimeImmutable::class)]
    private ?DateTimeImmutable $readAt = null;

    #[ORM\ManyToOne(inversedBy: 'readings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Notification $notification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    #[ORM\PrePersist]
    public function setReadAt(): self
    {
        $this->readAt = new DateTimeImmutable();

        return $this;
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

    public function getNotificationId(): int
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
