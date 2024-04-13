<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[OA\Schema(description: 'User entity representing a user in the system.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const GROUP_GET_USERS = 'get:users';

    public const GROUP_GET_USER_LIMITED = 'get:user-limited';

    public const GROUP_GET_USER = 'get:user';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_GET_USER, self::GROUP_GET_USERS])]
    #[OA\Property(description: 'The unique identifier of the user.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups([self::GROUP_GET_USER_LIMITED, self::GROUP_GET_USER, self::GROUP_GET_USERS])]
    #[OA\Property(description: 'The username of the user.', maxLength: 180)]
    private ?string $username = null;

    /**
     * @var array<string>
     */
    #[ORM\Column]
    #[Groups([self::GROUP_GET_USER_LIMITED, self::GROUP_GET_USER, self::GROUP_GET_USERS])]
    #[OA\Property(description: 'The roles of the user.', type: 'array', items: new OA\Items(type: 'string'))]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_GET_USER_LIMITED, self::GROUP_GET_USER, self::GROUP_GET_USERS])]
    #[OA\Property(description: 'The group of the user.', ref: '#/components/schemas/Group.item')]
    #[MaxDepth(2)]
    private ?Group $group = null;

    /**
     * @var Collection<int, Mount>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Mount::class, cascade: ['persist'])]
    #[OA\Property(description: 'The mounts of the user.', type: 'array', items: new OA\Items(ref: '#/components/schemas/Mount.item'))]
    #[Groups([self::GROUP_GET_USER_LIMITED, self::GROUP_GET_USER])]
    #[MaxDepth(2)]
    private Collection $mounts;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Service::class, cascade: ['persist'])]
    #[OA\Property(description: 'The services of the user.', type: 'array', items: new OA\Items(ref: '#/components/schemas/Service.item'))]
    #[Groups([self::GROUP_GET_USER_LIMITED, self::GROUP_GET_USER])]
    #[MaxDepth(2)]
    private Collection $services;

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups([self::GROUP_GET_USER])]
    #[OA\Property(description: 'The email of the user.', maxLength: 180)]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups([self::GROUP_GET_USER, self::GROUP_GET_USERS])]
    #[OA\Property(description: 'The verification status of the user.', type: 'boolean')]
    private ?bool $isVerified = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Preference::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[OA\Property(description: 'The preferencess of the user.', ref: '#/components/schemas/Preference.item')]
    #[Groups([self::GROUP_GET_USER_LIMITED, self::GROUP_GET_USER])]
    private ?Preference $preferences = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups([self::GROUP_GET_USER_LIMITED, self::GROUP_GET_USER])]
    private ?string $apiKey = null;

    public function __construct()
    {
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

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): static
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
    public function getServices(): Collection
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

    public function getPreferences(): ?Preference
    {
        return $this->preferences;
    }

    public function setPreferences(?Preference $preferences): self
    {
        $this->preferences = $preferences;

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
