<?php

namespace App\Entity;

use OpenApi\Attributes as OA;
use App\Repository\LogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: '`log`')]
#[OA\Schema(description: 'Log entity representing a log in the system.')]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups([User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The unique identifier of the log.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups([User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The timestamp of the log.', format: 'date-time')]
    private ?\DateTimeImmutable $timestamp = null;

    #[ORM\Column(type: Types::STRING, length: 60)]
    #[Groups([User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The type of the log.', maxLength: 60)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The content of the log.', type: 'string')]
    private ?string $content = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, length: 39)]
    #[Groups([User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The IP address of the log.', type: 'string')]
    private ?string $ip_address = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): static
    {
        $this->ip_address = $ip_address;

        return $this;
    }
}
