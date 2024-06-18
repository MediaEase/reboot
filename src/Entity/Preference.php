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

use App\Repository\PreferenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: PreferenceRepository::class)]
#[ORM\Table(name: 'preference')]
#[OA\Schema(description: 'Preference entity representing user preferences.')]
class Preference
{
    public const GROUP_GET_PREFERENCES = 'get:preferences';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The unique identifier of the preference.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_PREFERENCES])]
    #[OA\Property(description: 'A list of pinned applications.', type: 'array', items: new OA\Items(type: 'string'))]
    private ?array $pinnedApps = null;

    #[ORM\Column(length: 5)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_PREFERENCES])]
    #[OA\Property(description: 'The display preference.', maxLength: 5)]
    private ?string $display = null;

    #[ORM\Column(length: 10)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_PREFERENCES])]
    #[OA\Property(description: 'The shell preference.', maxLength: 10)]
    private ?string $shell = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_PREFERENCES])]
    #[OA\Property(description: 'A list of selected widgets.', type: 'array', items: new OA\Items(type: 'string'))]
    private ?array $selectedWidgets = null;

    #[ORM\Column(length: 15)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_PREFERENCES])]
    #[OA\Property(description: 'The theme preference.', maxLength: 15)]
    private ?string $theme = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'preferences')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The user associated with these preferences.', ref: '#/components/schemas/User.item')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The backdrop preference.', maxLength: 255)]
    private ?string $backdrop = 'user-backdrop.jpg';

    #[ORM\Column(length: 255)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The avatar preference.', maxLength: 255)]
    private ?string $avatar = 'user-avatar.jpg';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_PREFERENCES])]
    #[OA\Property(description: 'Indicator if full app listing is enabled.', type: 'boolean')]
    private ?bool $isFullAppListingEnabled = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'Indicator if Gravatar is enabled.', type: 'boolean')]
    private ?bool $isGravatarEnabled = true;

    #[ORM\Column(type: Types::STRING, length: 90)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The primary mount path.', maxLength: 90)]
    private ?string $primaryMountPath = '/';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_PREFERENCES])]
    #[OA\Property(description: 'Indicator if verbosity is enabled.', type: 'boolean')]
    private ?bool $isVerbosityEnabled = false;

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
        if ($user->getPreferences() !== $this) {
            $user->setPreferences($this);
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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function isIsFullAppListingEnabled(): ?bool
    {
        return $this->isFullAppListingEnabled;
    }

    public function setFullAppListingEnabled(bool $isFullAppListingEnabled): static
    {
        $this->isFullAppListingEnabled = $isFullAppListingEnabled;

        return $this;
    }

    public function isGravatarEnabled(): ?bool
    {
        return $this->isGravatarEnabled;
    }

    public function setGravatarEnabled(bool $isGravatarEnabled): static
    {
        $this->isGravatarEnabled = $isGravatarEnabled;

        return $this;
    }

    public function getPrimaryMountPath(): ?string
    {
        return $this->primaryMountPath;
    }

    public function setPrimaryMountPath(string $primaryMountPath): static
    {
        $this->primaryMountPath = $primaryMountPath;

        return $this;
    }

    public function isVerbosityEnabled(): ?bool
    {
        return $this->isVerbosityEnabled;
    }

    public function setVerbosityEnabled(bool $isVerbosityEnabled): static
    {
        $this->isVerbosityEnabled = $isVerbosityEnabled;

        return $this;
    }
}
