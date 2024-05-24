<?php

declare(strict_types=1);

/*
 * This file is part of the MediaEase project.
 *
 * (c) Thomas Chauveau <contact.tomc@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\MountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: MountRepository::class)]
#[ORM\Table(name: '`mount`')]
#[OA\Schema(description: 'Mount entity representing a file system mount.')]
class Mount implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The unique identifier of the mount.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Groups([User::GROUP_GET_USER_LIMITED, User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The file system path of the mount.', maxLength: 80)]
    private ?string $path = null;

    #[ORM\Column]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'Indicator whether Rclone is used for the mount.', type: 'boolean')]
    private ?bool $rclone = null;

    #[ORM\ManyToOne(inversedBy: 'mounts')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: 'The user associated with the mount.', ref: '#/components/schemas/User.item')]
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

    public function __toString(): string
    {
        return $this->path ?? '';
    }
}
