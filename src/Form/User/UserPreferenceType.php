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

namespace App\Form\User;

use App\Entity\Preference;
use App\Form\Type\ToggleSwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

final class UserPreferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('isGravatarEnabled', ToggleSwitchType::class, [
                'mapped' => true,
            ])
            ->add('isFullAppListingEnabled', ToggleSwitchType::class, [
                'mapped' => true,
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Save',
                'icon_before' => 'flowbite:floppy-disk-outline',
                'button_class' => 'iconed-button bg-green-500 hover:bg-green-700 text-white font-bold pl-3 rounded h-[2.5rem] pr-[1.25rem]',
                'icon_class' => 'block w-6 h-6 button-icon',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Preference::class,
        ]);
    }
}
