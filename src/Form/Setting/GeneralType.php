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
use App\Form\Type\ToggleSwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class GeneralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $setting = $options['data'];

        $formBuilder
            ->add('siteName', TextType::class, [
                'label' => 'Site Name',
                'attr' => [
                    'placeholder' => $setting ? $setting->getSiteName() : 'Site Name',
                    'class' => 'w-full md:w-1/2 px-3 mb-6 md:mb-0',
                ],
                'help' => 'The name of the site',
            ])
            ->add('siteDescription', TextType::class, [
                'label' => 'Site description',
                'attr' => [
                    'placeholder' => $setting ? $setting->getSiteDescription() : 'Site description',
                ],
                'help' => 'The description of the site',
            ])
            ->add('rootUrl', UrlType::class, [
                'label' => 'Root URL',
                'attr' => [
                    'placeholder' => $setting ? $setting->getRootUrl() : 'Root URL',
                ],
                'help' => 'The root URL of the site',
            ])
            ->add('backdrop', DropzoneType::class, [
                'mapped' => false,
                'label' => 'Default Backdrop',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Drag and drop a file or click to browse',
                ],
            ])
            ->add('splashscreen', DropzoneType::class, [
                'mapped' => false,
                'label' => 'Splashscreen',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Drag and drop a file or click to browse',
                ],
            ])
            ->add('brand', DropzoneType::class, [
                'mapped' => false,
                'label' => 'Brand',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Drag and drop a file or click to browse',
                ],
            ])
            ->add('appstore', DropzoneType::class, [
                'mapped' => false,
                'label' => 'Appstore',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Drag and drop a file or click to browse',
                ],
            ])
            ->add('netInterface', ChoiceType::class, [
                'label' => 'Network interface',
                'choices' => array_flip($options['interfaces']),
                'data' => $setting ? $setting->getNetInterface() : 'eth0',
                'help' => 'The network interface to use in the network widget',
            ])
            ->add('isRegistrationEnabled', ToggleSwitchType::class, [
                'label' => 'Enable Registration',
                'data' => $setting ? $setting->isRegistrationEnabled() : true,
                'help' => 'Allow users to register',
            ])
            ->add('isWelcomeEmailEnabled', ToggleSwitchType::class, [
                'label' => 'Enable Welcome Email',
                'data' => $setting ? $setting->isWelcomeEmailEnabled() : true,
                'help' => 'Send a welcome email to new users',
            ])
            ->add('isEmailVerificationEnabled', ToggleSwitchType::class, [
                'label' => 'Enable Email Verification',
                'data' => $setting ? $setting->isEmailVerificationEnabled() : true,
                'help' => 'Require email verification for new users',
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
            'interfaces' => [],
        ]);
    }
}
