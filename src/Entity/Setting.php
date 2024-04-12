<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;


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

    #[ORM\Column(length: 60)]
    private ?string $siteDescription = null;

    #[ORM\Column(length: 80)]
    private ?string $backdrop = null;

    #[ORM\Column(length: 80)]
    private ?string $logo = null;

    #[ORM\Column(length: 6)]
    private ?string $defaultQuota = null;

    #[ORM\Column(length: 20)]
    private ?string $netInterface = null;

    #[ORM\Column]
    private ?bool $registrationEnabled = null;

    #[ORM\Column]
    private ?bool $welcomeEmail = null;

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

    public function getBackdrop(): ?string
    {
        return $this->backdrop;
    }

    public function setBackdrop(string $backdrop): static
    {
        $this->backdrop = $backdrop;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

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
}
