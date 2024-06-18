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

use App\Repository\StoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
#[ORM\Table(name: '`store`')]
#[OA\Schema(description: 'Store entity representing a store in the system.')]
class Store
{
    public const GROUP_GET_STORES = 'get_stores';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups([self::GROUP_GET_STORES])]
    #[OA\Property(description: 'The unique identifier of the App Store object.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([self::GROUP_GET_STORES])]
    #[OA\Property(description: 'The description of the application.')]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups([self::GROUP_GET_STORES])]
    #[OA\Property(description: 'Indicator whether the application isfor Pro users only.', type: 'boolean')]
    private ?bool $isPro = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups([self::GROUP_GET_STORES])]
    #[OA\Property(description: 'Indicator whether the application is available through the App Store.', type: 'boolean')]
    private ?bool $isAvailable = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Groups([self::GROUP_GET_STORES])]
    #[OA\Property(description: 'The type of the application.', maxLength: 50)]
    private ?string $type = null;

    #[ORM\OneToOne(mappedBy: 'store', cascade: ['persist', 'remove'])]
    #[Groups(Application::GROUP_GET_APPLICATIONS)]
    #[OA\Property(description: 'The application associated with the App Store object.', ref: '#/components/schemas/Application.item')]
    private ?Application $application = null;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'stores')]
    #[OA\Property(description: 'The groups associated with the App Store object.', type: 'array', items: new OA\Items(ref: '#/components/schemas/Group.item'))]
    private ?Collection $groups;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups([self::GROUP_GET_STORES])]
    #[OA\Property(description: 'The details of the application (documentation, website, ...)', type: 'json')]
    private ?array $details = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups([self::GROUP_GET_STORES])]
    #[OA\Property(description: 'Indicator whether the application is available for a multi-user usage', type: 'boolean')]
    private ?bool $isMultiUser = null;

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

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(?array $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function isMultiUser(): ?bool
    {
        return $this->isMultiUser;
    }

    public function setMultiUser(bool $isMultiUser): static
    {
        $this->isMultiUser = $isMultiUser;

        return $this;
    }
}
