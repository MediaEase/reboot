<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WidgetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WidgetRepository::class)]
class Widget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Groups(['widget:info'])]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    #[Groups(['widget:info'])]
    private ?string $altName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['widget:info'])]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAltName(): ?string
    {
        return $this->altName;
    }

    public function setAltName(string $altName): static
    {
        $this->altName = $altName;

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
}
