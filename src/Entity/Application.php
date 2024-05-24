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

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\Table(name: '`application`')]
#[OA\Schema(description: 'Application entity representing an application in the system.')]
class Application
{
    public const GROUP_GET_APPLICATIONS = 'get_applications';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_APPLICATIONS])]
    #[OA\Property(description: 'The unique identifier of the application.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_APPLICATIONS, User::GROUP_GET_USER_LIMITED, Store::GROUP_GET_STORES])]
    #[OA\Property(description: 'The name of the application.', maxLength: 60)]
    private ?string $name = null;

    #[ORM\Column(length: 60)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_APPLICATIONS, User::GROUP_GET_USER_LIMITED, Store::GROUP_GET_STORES])]
    #[OA\Property(description: 'The alternative name of the application.', maxLength: 60)]
    private ?string $altname = null;

    #[ORM\Column(length: 120)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_APPLICATIONS, User::GROUP_GET_USER_LIMITED, Store::GROUP_GET_STORES])]
    #[OA\Property(description: 'The logo of the application.', maxLength: 120)]
    private ?string $logo = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: Service::class)]
    #[OA\Property(description: 'The services associated with the application.', type: 'array', items: new OA\Items(ref: '#/components/schemas/Service.item'))]
    private Collection $services;

    #[ORM\OneToOne(inversedBy: 'application', cascade: ['persist', 'remove'])]
    #[OA\Property(description: 'The store associated with the application.', ref: '#/components/schemas/Store.item')]
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
