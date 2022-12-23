<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NotificationReceiverRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotificationReceiverRepository::class)]
#[UniqueEntity(
    fields: ['notification', 'email'],
    message: 'Email must be unique.')
]
class NotificationReceiver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(type: 'int')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Email]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[Assert\Type(type: 'string')]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'receivers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Notification $notification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
}
