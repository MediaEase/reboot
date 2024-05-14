<?php

namespace App\Form\User;

use App\Entity\Preference;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('backdrop', DropzoneType::class, [
                'mapped' => false,
                'label' => 'label.fichier',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Drag and drop a file or click to browse',
                ],
            ])
            ->add('avatar', DropzoneType::class, [
                'mapped' => false,
                'label' => 'label.fichier',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Drag and drop a file or click to browse',
                ],
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
