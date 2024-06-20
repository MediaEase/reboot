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

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfonycasts\DynamicForms\DynamicFormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder = new DynamicFormBuilder($formBuilder);

        $formBuilder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Name',
                    'help' => 'The name of the application',
                ],
            ])
            ->add('logo', DropzoneType::class, [
                'mapped' => false,
                'label' => 'Logo',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Drag and drop a file or click to browse',
                ],
            ])
            ->add('services', CollectionType::class, [
                'entry_type' => ServiceType::class,
                'allow_add' => true,
                'required' => false,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Services',
                'attr' => [
                    'class' => 'services-collection',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Add Application',
                'icon_before' => 'heroicons:plus',
                'button_class' => 'iconed-button bg-green-500 hover:bg-green-700 text-white font-bold pl-3 rounded h-[2.5rem] pr-[1.25rem]',
                'icon_class' => 'w-6 h-6 fill-white mr-2 button-icon',
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
