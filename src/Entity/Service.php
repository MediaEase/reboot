<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: '`service`')]
#[OA\Schema(description: 'Service entity representing a service in the system.')]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The unique identifier of the service.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, User::GROUP_GET_USER_LIMITED, User::GROUP_GET_USER])]
    #[OA\Property(description: 'The name of the service.', maxLength: 60)]
    private ?string $name = null;

    #[ORM\Column(length: 40, nullable: true)]
    #[Groups([User::GROUP_GET_USER_LIMITED])]
    #[OA\Property(description: 'The version of the service.', maxLength: 40)]
    private ?string $version = null;

    #[ORM\Column(length: 25, nullable: true)]
    #[Groups([User::GROUP_GET_USER_LIMITED])]
    #[OA\Property(description: 'The status of the service.', maxLength: 25)]
    private ?string $status = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Groups([User::GROUP_GET_USER_LIMITED])]
    #[OA\Property(description: 'The API key of the service.', maxLength: 80)]
    private ?string $apikey = null;

    /**
     * @var ?array<string>
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USER_LIMITED])]
    #[OA\Property(description: 'The ports used by the service.', type: 'array', items: new OA\Items(type: 'string'))]
    private ?array $ports = null;

    /**
     * @var ?array<string>
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USER_LIMITED])]
    #[OA\Property(description: 'The configuration of the service.', type: 'array', items: new OA\Items(type: 'string'))]
    private ?array $configuration = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: 'The application associated with the service.', ref: '#/components/schemas/Application.item')]
    #[Groups([User::GROUP_GET_USER_LIMITED])]
    private ?Application $application = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: 'The user associated with the service.', ref: '#/components/schemas/User.item')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'childServices')]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, User::GROUP_GET_USER_LIMITED])]
    #[OA\Property(description: 'The parent service, if any.', ref: '#/components/schemas/Service.item')]
    private ?self $parentService = null;

    #[ORM\OneToMany(mappedBy: 'parentService', targetEntity: self::class)]
    #[OA\Property(description: 'The child services of this service.', type: 'array', items: new OA\Items(ref: '#/components/schemas/Service.item'))]
    private Collection $childServices;

    public function __construct()
    {
        $this->childServices = new ArrayCollection();
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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return ?array<string>
     */
    public function getPorts(): ?array
    {
        return $this->ports;
    }

    /**
     * @param ?array<string> $ports
     */
    public function setPorts(?array $ports): static
    {
        $this->ports = $ports;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): static
    {
        $this->application = $application;

        return $this;
    }

    public function getApikey(): ?string
    {
        return $this->apikey;
    }

    public function setApikey(?string $apikey): static
    {
        $this->apikey = $apikey;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return ?array<string>
     */
    public function getConfiguration(): ?array
    {
        return $this->configuration;
    }

    /**
     * @param ?array<string> $configuration
     */
    public function setConfiguration(?array $configuration): static
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getParentService(): ?self
    {
        return $this->parentService;
    }

    public function setParentService(?self $parentService): static
    {
        $this->parentService = $parentService;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildServices(): Collection
    {
        return $this->childServices;
    }

    public function addChildService(self $childService): static
    {
        if (! $this->childServices->contains($childService)) {
            $this->childServices->add($childService);
            $childService->setParentService($this);
        }

        return $this;
    }

    public function removeChildService(self $childService): static
    {
        // set the owning side to null (unless already changed)
        if ($this->childServices->removeElement($childService) && $childService->getParentService() === $this) {
            $childService->setParentService(null);
        }

        return $this;
    }
}
