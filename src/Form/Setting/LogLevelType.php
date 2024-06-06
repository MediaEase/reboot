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

namespace App\Form\Setting;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class LogLevelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $setting = $options['data'];

        $formBuilder
            ->add('defaultLogLevel', ChoiceType::class, [
                'choices'  => [
                    'All' => 'ALL',
                    'Debug' => 'DEBUG',
                    'Info' => 'INFO',
                    'Notice' => 'NOTICE',
                    'Warning' => 'WARNING',
                    'Error' => 'ERROR',
                    'Critical' => 'CRITICAL',
                    'Alert' => 'ALERT',
                    'Emergency' => 'EMERGENCY',
                ],
                'data' => $setting->getDefaultLogLevel(),
                'multiple' => false,
                'empty_data' => $setting->getDefaultLogLevel(),
            ])
            ->add('logRefreshDelay', ChoiceType::class, [
                'choices'  => [
                    '1 second' => 1,
                    '5 seconds' => 5,
                    '10 seconds' => 10,
                    '30 seconds' => 30,
                ],
                'multiple' => false,
                'data' => $setting->getLogRefreshDelay(),
                'empty_data' => $setting->getLogRefreshDelay(),
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Save',
                'icon_before' => 'flowbite:floppy-disk-outline',
                'button_class' => 'iconed-button bg-green-500 hover:bg-green-700 text-white font-bold pl-3 rounded h-[2.5rem] pr-[1.25rem]',
                'icon_class' => 'w-8 h-8 fill-white button-icon',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}
