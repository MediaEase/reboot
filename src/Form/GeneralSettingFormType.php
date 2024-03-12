<?php

namespace App\Form;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class GeneralSettingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('siteTitle', TextType::class, [
                'label' => 'Site title',
                'attr' => [
                    'placeholder' => 'Site title',
                    'class' => 'w-full md:w-1/2 px-3 mb-6 md:mb-0',
                ],
            ])
            ->add('siteDescription', TextType::class, [
                'label' => 'Site description',
                'attr' => [
                    'placeholder' => 'Site description',
                ],
            ])
            ->add('emailSenderAddress', TextType::class, [
                'label' => 'Email sender address',
                'attr' => [
                    'placeholder' => 'Email sender address',
                ],
            ])
            ->add('emailSenderName', TextType::class, [
                'label' => 'Email sender name',
                'attr' => [
                    'placeholder' => 'Email sender name',
                ],
            ])
            ->add('webrootUrl', UrlType::class, [
                'label' => 'Webroot URL',
                'attr' => [
                    'placeholder' => 'Webroot URL',
                ],
            ])
            ->add('mountPoint', TextType::class, [
                'label' => 'Mount point',
                'attr' => [
                    'placeholder' => 'Mount point',
                ],
            ])
            ->add('netInterface', TextType::class, [
                'label' => 'Network interface',
                'attr' => [
                    'placeholder' => 'Network interface',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}
