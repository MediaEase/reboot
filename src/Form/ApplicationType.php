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

use App\Entity\Application;
use App\Entity\Group;
use App\Entity\Store;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('name')
            ->add('altname')
            ->add('logo')
            ->add('type')
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('store', EntityType::class, [
                'class' => Store::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
