<?php

namespace App\Form\Setting;

use App\Entity\Group;
use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

final class NewGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('name', TextType::class, [
                'label' => 'Group Name',
                'attr' => [
                    'placeholder' => 'New Group Name',
                    'class' => 'w-full md:w-1/2 px-3 mb-6 md:mb-0',
                ],
                'help' => 'The name of the new group',
            ])
            ->add('stores', EntityType::class, [
                'label' => 'Applications',
                'class' => Application::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Create',
                'icon_before' => 'bookmark',
                'button_class' => 'bg-gradient-to-r from-green-400 to-green-600 text-white font-bold py-2 px-4 rounded',
                'icon_class' => 'w-5 h-5 fill-save stroke-save button-icon',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Group::class,
            'interfaces' => [],
        ]);
    }
}
