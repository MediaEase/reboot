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

use App\Repository\TranscodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: TranscodeRepository::class)]
#[ORM\Table(name: 'transcode')]
#[OA\Schema(description: 'Transcode entity representing a transcode in the system.')]
class Transcode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[OA\Property(description: 'The unique identifier of the transcode.', format: 'int')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The user associated with the transcode.', ref: '#/components/schemas/User.item')]
    private ?User $user = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], inversedBy: 'transcode', targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, User::GROUP_GET_USER_LIMITED, User::GROUP_GET_USER])]
    #[OA\Property(description: 'The service associated with the transcode.', ref: '#/components/schemas/Service.item')]
    private ?Service $service = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[OA\Property(description: 'Indicator whether the transcode is enabled.', type: 'boolean', default: false)]
    private ?bool $isEnabled = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }
}
