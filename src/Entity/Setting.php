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

use OpenApi\Attributes as OA;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SettingRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    public const GROUP_GET_SETTINGS = 'get:settings';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 70, type: Types::STRING)]
    #[OA\Property(description: 'The site name preference.', maxLength: 70)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $siteName = null;

    #[ORM\Column(length: 70, type: Types::STRING)]
    #[OA\Property(description: 'The root URL preference.', maxLength: 70)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $rootUrl = null;

    #[ORM\Column(length: 255, type: Types::STRING)]
    #[OA\Property(description: 'The site description preference.', maxLength: 255)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $siteDescription = null;

    #[ORM\Column(length: 6, type: Types::STRING)]
    #[OA\Property(description: 'The default quota preference.', maxLength: 6, pattern: '^\d+(GB|TB)$', example: '10GB', default: '10GB')]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $defaultQuota = null;

    #[ORM\Column(length: 20, type: Types::STRING)]
    #[OA\Property(description: 'The network interface preference.', maxLength: 20)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $netInterface = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[OA\Property(description: 'The registration preference.', default: false)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?bool $registrationEnabled = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[OA\Property(description: 'The welcome email preference.', default: true)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?bool $welcomeEmailEnabled = null;

    #[ORM\Column(length: 60, type: Types::STRING)]
    #[OA\Property(description: 'The brand logo preference.', maxLength: 60)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $brand = null;

    #[ORM\Column(length: 60, type: Types::STRING)]
    #[OA\Property(description: 'The favicon preference.', maxLength: 60)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $favicon = null;

    #[ORM\Column(length: 60, type: Types::STRING)]
    #[OA\Property(description: 'The appstore banner preference.', maxLength: 60)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $appstore = null;

    #[ORM\Column(length: 60, type: Types::STRING)]
    #[OA\Property(description: 'The splashscreen preference.', maxLength: 60)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private ?string $splashscreen = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true, 'nullable' => false])]
    #[OA\Property(description: 'The email verification preference.', default: true)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private bool $emailVerificationEnabled = true;

    #[ORM\Column(length: 10, type: Types::STRING, options: ['default' => 'all'])]
    #[OA\Property(description: 'The default log level preference.', maxLength: 10, default: 'all', pattern: '^(all|debug|info|notice|warning|error|critical|alert|emergency)$', example: 'all')]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private string $defaultLogLevel = 'all';

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 5], nullable: true)]
    #[OA\Property(description: 'The log refresh delay preference.', format: 'int', default: 5)]
    #[Groups([self::GROUP_GET_SETTINGS])]
    private int $logRefreshDelay = 5;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    public function setSiteName(string $siteName): static
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function getRootUrl(): ?string
    {
        return $this->rootUrl;
    }

    public function setRootUrl(string $rootUrl): static
    {
        $this->rootUrl = $rootUrl;

        return $this;
    }

    public function getSiteDescription(): ?string
    {
        return $this->siteDescription;
    }

    public function setSiteDescription(string $siteDescription): static
    {
        $this->siteDescription = $siteDescription;

        return $this;
    }

    public function getDefaultQuota(): ?string
    {
        return $this->defaultQuota;
    }

    public function setDefaultQuota(string $defaultQuota): static
    {
        $this->defaultQuota = $defaultQuota;

        return $this;
    }

    public function getNetInterface(): ?string
    {
        return $this->netInterface;
    }

    public function setNetInterface(string $netInterface): static
    {
        $this->netInterface = $netInterface;

        return $this;
    }

    public function isRegistrationEnabled(): ?bool
    {
        return $this->registrationEnabled;
    }

    public function setRegistrationEnabled(bool $registrationEnabled): static
    {
        $this->registrationEnabled = $registrationEnabled;

        return $this;
    }

    public function isWelcomeEmailEnabled(): ?bool
    {
        return $this->welcomeEmailEnabled;
    }

    public function setWelcomeEmailEnabled(bool $welcomeEmailEnabled): static
    {
        $this->welcomeEmailEnabled = $welcomeEmailEnabled;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    public function setFavicon(string $favicon): static
    {
        $this->favicon = $favicon;

        return $this;
    }

    public function getAppstore(): ?string
    {
        return $this->appstore;
    }

    public function setAppstore(string $appstore): static
    {
        $this->appstore = $appstore;

        return $this;
    }

    public function getSplashscreen(): ?string
    {
        return $this->splashscreen;
    }

    public function setSplashscreen(string $splashscreen): static
    {
        $this->splashscreen = $splashscreen;

        return $this;
    }

    public function isEmailVerificationEnabled(): ?bool
    {
        return $this->emailVerificationEnabled;
    }

    public function setEmailVerificationEnabled(bool $emailVerificationEnabled): static
    {
        $this->emailVerificationEnabled = $emailVerificationEnabled;

        return $this;
    }

    public function getDefaultLogLevel(): ?string
    {
        return $this->defaultLogLevel;
    }

    public function setDefaultLogLevel(string $defaultLogLevel): static
    {
        $this->defaultLogLevel = $defaultLogLevel;

        return $this;
    }

    public function getLogRefreshDelay(): ?int
    {
        return $this->logRefreshDelay;
    }

    public function setLogRefreshDelay(int $logRefreshDelay): static
    {
        $this->logRefreshDelay = $logRefreshDelay;

        return $this;
    }
}
