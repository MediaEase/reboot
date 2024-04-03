<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
class Store
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['store:info', 'application:info', 'services:info', 'group:info'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['store:info', 'application:info'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['store:info', 'application:info', 'group:info'])]
    private ?bool $isPro = null;

    #[ORM\Column]
    #[Groups(['store:info', 'application:info', 'group:info'])]
    private ?bool $isAvailable = null;

    #[ORM\Column(length: 50)]
    #[Groups(['store:info', 'application:info', 'group:info', 'user:info'])]
    private ?string $type = null;

    #[ORM\OneToOne(mappedBy: 'store', cascade: ['persist', 'remove'])]
    #[Groups(['store:info', 'group:info'])]
    private ?Application $application = null;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'stores')]
    private Collection $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isPro(): ?bool
    {
        return $this->isPro;
    }

    public function setIsPro(bool $isPro): static
    {
        $this->isPro = $isPro;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

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

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): static
    {
        if (! $application instanceof Application && $this->application instanceof Application) {
            $this->application->setStore(null);
        }

        if ($application instanceof Application && $application->getStore() !== $this) {
            $application->setStore($this);
        }

        $this->application = $application;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (! $this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addStore($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            $group->removeStore($this);
        }

        return $this;
    }
}
