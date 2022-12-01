<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Exception;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Notification
{
    public const NOTIFICATION_EMAIL_TYPE = 'EMAIL';
    public const HTTP_METHODS = [ 'GET', 'POST'];
    public const HTTP_FORM_METHODS = [ 'GET' => 'GET', 'POST' => 'POST'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(type: 'int')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Type(type: 'string')]
    private ?string $url = null;

    #[ORM\OneToMany(
        mappedBy: 'notification',
        targetEntity: NotificationReceiver::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Assert\Count(
        min: 1,
        minMessage: 'You must specify at least one email')
    ]
    private Collection $receivers;

    #[ORM\OneToMany(
        mappedBy: 'notification',
        targetEntity: NotificationReading::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Collection $readings;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Type(type: 'string')]
    #[Assert\Choice(choices: [self::NOTIFICATION_EMAIL_TYPE])]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Type(type: 'string')]
    #[Assert\Choice(choices: self::HTTP_METHODS)]
    private ?string $httpMethod = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Assert\Type(type: 'int')]
    private ?int $sendingFrequency = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'boolean')]
    private ?bool $isActive = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: DateTimeImmutable::class)]
    private ?DateTimeImmutable $sendingDate = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: DateTimeImmutable::class)]
    private ?DateTimeImmutable $sentAt = null;

    #[ORM\Column]
    #[Assert\Type(type: DateTimeImmutable::class)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->receivers = new ArrayCollection();
        $this->readings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getReceivers(): Collection
    {
        return $this->receivers;
    }

    public function addReceiver(NotificationReceiver $receiver): self
    {
        if (!$this->receivers->contains($receiver)) {
            $this->receivers->add($receiver);
            $receiver->setNotification($this);
        }

        return $this;
    }

    public function removeReceiver(NotificationReceiver $receiver): self
    {
        if (
            $this->receivers->removeElement($receiver) &&
            $receiver->getNotification() === $this
        ) {
            $receiver->setNotification(null);
        }

        return $this;
    }

    public function getReadings(): Collection
    {
        return $this->readings;
    }

    public function addReading(NotificationReading $reading): self
    {
        if (!$this->readings->contains($reading)) {
            $this->readings->add($reading);
            $reading->setNotification($this);
        }

        return $this;
    }

    public function removeReading(NotificationReading $reading): self
    {
        if (
            $this->readings->removeElement($reading) &&
            $reading->getNotification() === $this
        ) {
            $reading->setNotification(null);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setHttpMethod(string $httpMethod): self
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    public function getHttpMethod(): ?string
    {
        return $this->httpMethod;
    }

    public function getSendingFrequency(): ?int
    {
        return $this->sendingFrequency;
    }

    public function setSendingFrequency(int $sendingFrequency): self
    {
        $this->sendingFrequency = $sendingFrequency;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getSendingDate(): ?DateTimeImmutable
    {
        return $this->sendingDate;
    }

    public function setSendingDate(): self
    {
        $this->sendingDate = (new DateTimeImmutable())
            ->modify('+ ' . $this->sendingFrequency . ' hours');

        return $this;
    }

    public function getSentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }

    #[ORM\PrePersist]
    public function initSendingDate(): self
    {
        if (!$this->sendingFrequency) {
            throw new Exception('Sending frequency has not been set.');
        }

        $this->sendingDate = (new DateTimeImmutable())
            ->modify(
                sprintf(
                    '+ %d hours',
                    $this->sendingFrequency
                )
            );

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function getHasFailedReadings(): bool
    {
        $hasFailed = false;
        if ($this->readings->count()) {
            $this->readings->map(function (NotificationReading $reading) use (&$hasFailed): void {
                if ($reading->isFailed()) {
                    $hasFailed = true;
                }
            });
        }

        return $hasFailed;
    }
}
