<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    private ?string $siteName = null;

    #[ORM\Column(length: 70)]
    private ?string $rootUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $siteDescription = null;

    #[ORM\Column(length: 6)]
    private ?string $defaultQuota = null;

    #[ORM\Column(length: 20)]
    private ?string $netInterface = null;

    #[ORM\Column]
    private ?bool $registrationEnabled = null;

    #[ORM\Column]
    private ?bool $welcomeEmail = null;

    #[ORM\Column(length: 60)]
    #[OA\Property(description: 'The brand logo preference.', maxLength: 60)]
    private ?string $brand = null;

    #[ORM\Column(length: 60)]
    #[OA\Property(description: 'The favicon preference.', maxLength: 60)]
    private ?string $favicon = null;

    #[ORM\Column(length: 60)]
    #[OA\Property(description: 'The appstore banner preference.', maxLength: 60)]
    private ?string $appstore = null;

    #[ORM\Column(length: 60)]
    #[OA\Property(description: 'The splashscreen preference.', maxLength: 60)]
    private ?string $splashscreen = null;

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

    public function isWelcomeEmail(): ?bool
    {
        return $this->welcomeEmail;
    }

    public function setWelcomeEmail(bool $welcomeEmail): static
    {
        $this->welcomeEmail = $welcomeEmail;

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
}
