<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

final class SettingFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $objectManager): void
    {
        $setting = new Setting();
        $setting->setSiteName('My Cloud');
        $setting->setRootUrl('https://mycloud.example.com');
        $setting->setSiteDescription('My Cloud');
        $setting->setBackdrop('default-backdrop.png');
        $setting->setLogo('default-favicon.png');
        $setting->setDefaultQuota('10G');
        $setting->setNetInterface('eth0');
        $setting->setRegistrationEnabled(true);
        $setting->setWelcomeEmail(true);

        $objectManager->persist($setting);

        $objectManager->flush();
    }

    public static function getGroups(): array
    {
        return ['prod', 'ci'];
    }
}
