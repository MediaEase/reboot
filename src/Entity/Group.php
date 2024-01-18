<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['group:info', 'application:info'])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups(['group:info', 'application:info', 'services:info,', 'store:info'])]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: User::class)]
    #[Groups(['group:info', 'application:info', 'user:info'])]
    private Collection $users;

    /**
     * @var Collection<int, Store>
     */
    #[ORM\ManyToMany(targetEntity: Store::class, inversedBy: 'groups')]
    #[Groups(['group:info', 'store:info', 'application:info'])]
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
            $user->setAppGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        // set the owning side to null (unless already changed)
        if (! $this->users->removeElement($user)) {
            return $this;
        }

        if ($user->getAppGroup() !== $this) {
            return $this;
        }

        $user->setAppGroup(null);

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
}
