<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enums\HttpMethod;
use App\Entity\Enums\NotificationType;
use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Exception;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Notification
{
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

    #[ORM\Column(length: 10, enumType: NotificationType::class)]
    #[Assert\Type(type: NotificationType::class)]
    #[Assert\Choice(choices: [NotificationType::EMAIL])]
    private NotificationType $type;

    #[ORM\Column(length: 10, enumType: HttpMethod::class)]
    #[Assert\Type(type: HttpMethod::class)]
    #[Assert\Choice(choices: [HttpMethod::GET, HttpMethod::POST])]
    private HttpMethod $httpMethod;

    #[ORM\Column]
    #[Assert\Positive]
    #[Assert\Type(type: 'int')]
    private ?int $sendingFrequency = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'boolean')]
    private ?bool $isActive = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: DateTime::class)]
    private ?DateTime $sendingDate = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: DateTime::class)]
    private ?DateTime $sentAt = null;

    #[ORM\Column]
    #[Assert\Type(type: DateTime::class)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: DateTime::class)]
    private ?DateTime $updatedAt = null;

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

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function setType(NotificationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setHttpMethod(HttpMethod $httpMethod): self
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    public function getHttpMethod(): HttpMethod
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

    public function getSendingDate(): ?DateTime
    {
        return $this->sendingDate;
    }

    public function setSendingDate(): self
    {
        $this->sendingDate = (new DateTime())
            ->modify('+ ' . $this->sendingFrequency . ' hours');

        return $this;
    }

    public function getSentAt(): ?DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTime $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
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

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    #[ORM\PrePersist]
    public function initSendingDate(): self
    {
        if (!$this->sendingFrequency) {
            throw new Exception('Sending frequency has not been set.');
        }

        $this->sendingDate = (new DateTime())
            ->modify(
                sprintf(
                    '+ %d hours',
                    $this->sendingFrequency
                )
            );

        return $this;
    }
}
