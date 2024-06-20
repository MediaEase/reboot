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

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('name', TextType::class, [
                'label' => 'Service Name',
                'attr' => [
                    'placeholder' => 'Service Name',
                    'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                ],
            ])
            ->add('apikey', TextType::class, [
                'label' => 'API Key',
                'required' => false,
                'attr' => [
                    'placeholder' => 'API Key',
                    'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                ],
            ])
            ->add('ports', CollectionType::class, [
                'entry_type' => IntegerType::class,
                'label' => 'Ports',
                'prototype_name' => '__port__',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'attr' => [
                        'placeholder' => 'Enter port number',
                        'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                    ],
                ],
            ])
            ->add('configuration', CollectionType::class, [
                'entry_type' => TextType::class,
                'label' => 'Configuration',
                'prototype_name' => '__config__',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'attr' => [
                        'placeholder' => 'Enter configuration value',
                    ],
                ],
            ])
            ->add('subdomain', TextType::class, [
                'label' => 'Subdomain',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Subdomain',
                    'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                ],
            ])
            ->add('config_path', TextType::class, [
                'label' => 'Config Path',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Config Path',
                    'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                ],
            ])
            ->add('database_path', TextType::class, [
                'label' => 'Database Path',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Database Path',
                    'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                ],
            ])
            ->add('caddyfile_path', TextType::class, [
                'label' => 'Caddyfile Path',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Caddyfile Path',
                    'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                ],
            ])
            ->add('backup_path', TextType::class, [
                'label' => 'Backup Path',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Backup Path',
                    'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                ],
            ])
            ->add('root_url', UrlType::class, [
                'label' => 'Root URL',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Root URL',
                    'class' => 'block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
