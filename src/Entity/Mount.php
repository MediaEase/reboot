<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MountRepository::class)]
class Mount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['mount:info'])]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Groups(['user:info', 'mount:info', 'mount:update'])]
    private ?string $path = null;

    #[ORM\Column]
    #[Groups(['user:info', 'mount:info', 'mount:update'])]
    private ?bool $rclone = null;

    #[ORM\ManyToOne(inversedBy: 'mounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function isRclone(): ?bool
    {
        return $this->rclone;
    }

    public function setIsRclone(bool $rclone): static
    {
        $this->rclone = $rclone;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
