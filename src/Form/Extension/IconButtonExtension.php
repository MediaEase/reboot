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

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IconButtonExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ButtonType::class];
    }

    public function buildView(FormView $formView, FormInterface $form, array $options): void
    {
        $formView->vars['icon_before'] = $options['icon_before'];
        $formView->vars['icon_after'] = $options['icon_after'];
        $formView->vars['button_class'] = $options['button_class'];
        $formView->vars['icon_class'] = $options['icon_class'];
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'icon_before' => null,
            'icon_after' => null,
            'button_class' => null,
            'icon_class' => 'w-6 h-6 fill-white',
        ]);
    }
}
