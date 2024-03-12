<?php

namespace App\Form;

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

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['icon_before'] = $options['icon_before'];
        $view->vars['icon_after'] = $options['icon_after'];
        $view->vars['button_class'] = $options['button_class'];
        $view->vars['icon_class'] = $options['icon_class'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        'icon_before' => null,
        'icon_after' => null,
        'button_class' => null,
        'icon_class' => 'w-6 h-6 fill-white',
    ]);
    }
}
