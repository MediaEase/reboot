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

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[OA\Schema(description: 'Group entity representing a user group in the system.')]
class Group implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Store::GROUP_GET_STORES])]
    #[OA\Property(description: 'The unique identifier of the group.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups([Store::GROUP_GET_STORES, User::GROUP_GET_USER_LIMITED, User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The name of the group.', maxLength: 60)]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: User::class)]
    #[OA\Property(description: 'The users in the group.', type: 'array', items: new OA\Items(ref: '#/components/schemas/User.item'))]
    private Collection $users;

    /**
     * @var Collection<int, Store>
     */
    #[ORM\ManyToMany(targetEntity: Store::class, inversedBy: 'groups')]
    #[OA\Property(description: 'The stores associated with the group.', type: 'array', items: new OA\Items(ref: '#/components/schemas/Store.item'))]
    private Collection $stores;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->stores = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getusers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (! $this->users->contains($user)) {
            $this->users->add($user);
            $user->setGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        // set the owning side to null (unless already changed)
        if (! $this->users->removeElement($user)) {
            return $this;
        }

        if ($user->getGroup() !== $this) {
            return $this;
        }

        $user->setGroup(null);

        return $this;
    }

    /**
     * @return Collection<int, Store>
     */
    public function getStores(): Collection
    {
        return $this->stores;
    }

    public function addStore(Store $store): self
    {
        if (! $this->stores->contains($store)) {
            $this->stores->add($store);
            $store->addGroup($this);
        }

        return $this;
    }

    public function removeStore(Store $store): self
    {
        if ($this->stores->removeElement($store)) {
            $store->removeGroup($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
