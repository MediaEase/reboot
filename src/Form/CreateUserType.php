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

namespace App\Form;

use App\Entity\User;
use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

final class CreateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('username', TextType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                    'label' => 'Password',
                    'help' => 'Your password should be at least 12 characters long',
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'help' => 'Please repeat your password',
                ],
                'invalid_message' => 'The password fields must match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
            ->add('email', EmailType::class)
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
            ])
            // ->add('save', ButtonType::class, [
            //     'label' => 'Create',
            //     'icon_before' => 'lucide:user-plus',
            //     'button_class' => 'iconed-button bg-green-500 hover:bg-green-700 text-white font-bold pl-3 rounded h-[2.5rem] pr-[1.25rem]',
            //     'icon_class' => 'w-6 h-6 fill-white button-icon',
            // ]);
            ->add('save', ButtonType::class, [
                'label' => 'Create',
                'icon_before' => 'lucide:user-plus',
                'button_class' => 'iconed-button bg-green-500 hover:bg-green-700 text-white font-bold pl-3 rounded h-[2.5rem] pr-[1.25rem]',
                'icon_class' => 'w-6 h-6 fill-white button-icon',
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
