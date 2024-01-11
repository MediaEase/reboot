<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public \Doctrine\Common\Collections\ArrayCollection $preferences;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['application:info', 'user:info'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['application:info', 'user:info'])]
    private ?string $username = null;

    /**
     * @var array<string>
     */
    #[ORM\Column]
    #[Groups(['user:info'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $group = null;

    /**
     * @var Collection<int, Mount>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Mount::class, cascade: ['persist'])]
    #[Groups(['user:info'])]
    private Collection $mounts;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Service::class)]
    private Collection $services;

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['user:info'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:info'])]
    private ?bool $isVerified = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Preference::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user:info'])]
    private ?Preference $preference = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['user:info'])]
    private ?string $apiKey = null;

    public function __construct()
    {
        $this->preferences = new ArrayCollection();
        $this->mounts = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAppGroup(): ?Group
    {
        return $this->group;
    }

    public function setAppGroup(?Group $group): static
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return Collection<int, Mount>
     */
    public function getMounts(): Collection
    {
        return $this->mounts;
    }

    public function addMount(Mount $mount): static
    {
        if (! $this->mounts->contains($mount)) {
            $this->mounts->add($mount);
            $mount->setUser($this);
        }

        return $this;
    }

    public function removeMount(Mount $mount): static
    {
        // set the owning side to null (unless already changed)
        if (! $this->mounts->removeElement($mount)) {
            return $this;
        }

        if ($mount->getUser() !== $this) {
            return $this;
        }

        $mount->setUser(null);

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getService(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (! $this->services->contains($service)) {
            $this->services->add($service);
            $service->setUser($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        // set the owning side to null (unless already changed)
        if (! $this->services->removeElement($service)) {
            return $this;
        }

        if ($service->getUser() !== $this) {
            return $this;
        }

        $service->setUser(null);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPreference(): ?Preference
    {
        return $this->preference;
    }

    public function setPreference(Preference $preference): static
    {
        $this->preference = $preference;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}
