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

use App\Entity\Widget;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

final class WidgetFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $objectManager): void
    {
        $widgets = ['CPU Widget', 'Memory Widget', 'Disk Widget', 'Network Widget'];
        $altnames = ['cpu_1', 'mem_1', 'disk_1', 'net_3'];
        $types = ['cpu', 'mem', 'disk', 'net'];

        foreach ($widgets as $key => $widgetName) {
            $widget = new Widget();
            $widget->setName($widgetName);
            $widget->setAltName($altnames[$key]);
            $widget->setType($types[$key]);

            $objectManager->persist($widget);
            $this->addReference('widget-'.$widgetName, $widget);
        }

        $objectManager->flush();
    }

    public static function getGroups(): array
    {
        return ['ci', 'prod'];
    }
}
