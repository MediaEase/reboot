<?php

declare(strict_types=1);

namespace App\Form\Setting;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

final class SmtpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'mail_protocol',
                ChoiceType::class,
                [
                    'label' => 'forms.mail.label.mail_protocol',
                    'choices' => [
                        'forms.mail.choice.mail' => 'mail',
                        'forms.mail.choice.smtp' => 'smtp',
                    ],
                ]
            )
            ->add(
                'mail_parameters',
                TextType::class,
                [
                    'label' => 'forms.mail.label.mail_parameters',
                ]
            )
            ->add(
                'smtp_hostname',
                TextType::class,
                [
                    'label' => 'forms.mail.label.smtp_hostname',
                    'help' => 'form.help.smtp_hostname',
                ]
            )
            ->add(
                'smtp_username',
                TextType::class,
                [
                    'label' => 'forms.mail.label.smtp_username',
                ]
            )
            ->add(
                'smtp_password',
                PasswordType::class,
                [
                    'label' => 'forms.mail.label.smtp_password',
                ]
            )
            ->add(
                'smtp_port',
                IntegerType::class,
                [
                    'label' => 'forms.mail.label.smtp_port',
                ]
            )
            ->add(
                'smtp_timeout',
                IntegerType::class,
                [
                    'label' => 'forms.mail.label.smtp_timeout',
                ]
            )
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
    }
}
