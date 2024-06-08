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
        $setting->setBrand('default-favicon.png');
        $setting->setDefaultQuota('10G');
        $setting->setNetInterface('eth0');
        $setting->setRegistrationEnabled(true);
        $setting->setwelcomeEmailEnabled(true);
        $setting->setFavicon('default-favicon.png');
        $setting->setAppstore('default-appstore.png');
        $setting->setSplashscreen('default.png');
        $setting->isEmailVerificationEnabled(true);

        $objectManager->persist($setting);

        $objectManager->flush();
    }

    public static function getGroups(): array
    {
        return ['prod', 'ci'];
    }
}
