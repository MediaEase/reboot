<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['application:info', 'services:info', 'store:info'])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups(['application:info', 'services:info', 'store:info', 'group:info'])]
    private ?string $name = null;

    #[ORM\Column(length: 60)]
    #[Groups(['application:info', 'services:info', 'store:info'])]
    private ?string $altname = null;

    #[ORM\Column(length: 120)]
    #[Groups(['application:info', 'services:info', 'store:info'])]
    private ?string $logo = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: Service::class)]
    #[Groups(['application:info'])]
    private Collection $services;

    #[ORM\OneToOne(inversedBy: 'application', cascade: ['persist', 'remove'])]
    #[Groups(['application:info'])]
    private ?Store $store = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (! $this->services->contains($service)) {
            $this->services->add($service);
            $service->setApplication($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if (! $this->services->removeElement($service)) {
            return $this;
        }

        if ($service->getApplication() !== $this) {
            return $this;
        }

        $service->setApplication(null);

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): static
    {
        $this->store = $store;

        return $this;
    }
}
