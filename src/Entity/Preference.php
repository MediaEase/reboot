<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PreferenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PreferenceRepository::class)]
class Preference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['preferences:info'])]
    private ?int $id = null;

    /**
     * @var array<string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['user:info', 'preferences:info', 'preferences:update'])]
    private ?array $pinnedApps = null;

    #[ORM\Column(length: 5)]
    #[Groups(['user:info', 'preferences:info', 'preferences:update'])]
    private ?string $display = null;

    #[ORM\Column(length: 10)]
    #[Groups(['user:info', 'preferences:info', 'preferences:update'])]
    private ?string $shell = null;

    /**
     * @var array<string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['user:info', 'preferences:info', 'preferences:update'])]
    private ?array $selectedWidgets = null;

    #[ORM\Column(length: 15)]
    #[Groups(['user:info', 'preferences:info', 'preferences:update'])]
    private ?string $theme = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'preference')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:info', 'preferences:info', 'preferences:update'])]
    private ?string $backdrop = 'user-backdrop.jpg';

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ?array<string>
     */
    public function getPinnedApps(): ?array
    {
        return $this->pinnedApps;
    }

    /**
     * @param ?array<string> $pinnedApps
     */
    public function setPinnedApps(?array $pinnedApps): static
    {
        $this->pinnedApps = $pinnedApps;

        return $this;
    }

    public function getDisplay(): ?string
    {
        return $this->display;
    }

    public function setDisplay(string $display): static
    {
        $this->display = $display;

        return $this;
    }

    public function getShell(): ?string
    {
        return $this->shell;
    }

    public function setShell(string $shell): static
    {
        $this->shell = $shell;

        return $this;
    }

    /**
     * @return ?array<string>
     */
    public function getSelectedWidgets(): ?array
    {
        return $this->selectedWidgets;
    }

    /**
     * @param ?array<string> $selectedWidgets
     */
    public function setSelectedWidgets(?array $selectedWidgets): static
    {
        $this->selectedWidgets = $selectedWidgets;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        if ($user->getPreference() !== $this) {
            $user->setPreference($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getBackdrop(): ?string
    {
        return $this->backdrop;
    }

    public function setBackdrop(string $backdrop): static
    {
        $this->backdrop = $backdrop;

        return $this;
    }
}
