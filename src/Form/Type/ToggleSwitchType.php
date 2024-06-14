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

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * ToggleSwitchType is a custom form type that renders a toggle switch.
 */
final class ToggleSwitchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $formEvent): void {
            $data = $formEvent->getData();
            if ($data === null) {
                $formEvent->setData(false);
            }
        });
    }

    public function buildView(FormView $formView, FormInterface $form, array $options): void
    {
        $formView->vars['checked'] = $form->getData();
        $formView->vars['label'] = $options['label'];
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'label' => false,
            'required' => false,
        ]);
    }

    public function getParent(): string
    {
        return CheckboxType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'toggle_switch';
    }
}
