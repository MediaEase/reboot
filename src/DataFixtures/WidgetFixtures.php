<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Widget;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class WidgetFixtures extends Fixture
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
}
