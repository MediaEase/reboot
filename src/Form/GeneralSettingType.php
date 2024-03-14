<?php

namespace App\Form;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class GeneralSettingType extends AbstractType
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
            ])
            ->add('siteDescription', TextType::class, [
                'label' => 'Site description',
                'attr' => [
                    'placeholder' => $setting ? $setting->getSiteDescription() : 'Site description',
                ],
            ])
            ->add('rootUrl', UrlType::class, [
                'label' => 'Root URL',
                'attr' => [
                    'placeholder' => $setting ? $setting->getRootUrl() : 'Root URL',
                ],
            ])
            ->add('netInterface', TextType::class, [
                'label' => 'Network interface',
                'attr' => [
                    'placeholder' => $setting ? $setting->getNetInterface() : 'Network interface',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Save',
                'icon_before' => 'check',
                'button_class' => 'bg-gradient-to-r from-green-400 to-blue-500 text-white font-bold py-2 px-4 rounded',
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
