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
    #[Groups(['store:info', 'application:info', 'services:info'])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups(['store:info', 'application:info'])]
    private ?string $name = null;

    #[ORM\Column(length: 60)]
    #[Groups(['store:info', 'application:info'])]
    private ?string $altname = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['store:info', 'application:info'])]
    private ?string $description = null;

    #[ORM\Column(length: 30)]
    #[Groups(['store:info', 'application:info'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['store:info', 'application:info'])]
    private ?bool $isPro = null;

    #[ORM\Column]
    #[Groups(['store:info', 'application:info'])]
    private ?bool $isAvailable = null;

    /**
     * @var Collection<int, Application>
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Application::class)]
    private Collection $instances;

    public function __construct()
    {
        $this->instances = new ArrayCollection();
    }

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

    public function getAltname(): ?string
    {
        return $this->altname;
    }

    public function setAltname(string $altname): static
    {
        $this->altname = $altname;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    /**
     * @return Collection<int, Application>
     */
    public function getInstances(): Collection
    {
        return $this->instances;
    }

    public function addInstance(Application $application): static
    {
        if (! $this->instances->contains($application)) {
            $this->instances->add($application);
            $application->setStore($this);
        }

        return $this;
    }

    public function removeInstance(Application $application): static
    {
        // set the owning side to null (unless already changed)
        if (! $this->instances->removeElement($application)) {
            return $this;
        }

        if ($application->getStore() !== $this) {
            return $this;
        }

        $application->setStore(null);

        return $this;
    }
}
