<?php

declare(strict_types=1);

namespace App\Form\Setting;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

final class PhpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        foreach ($options['data'] as $key => $value) {
            $formFieldName = str_replace('.', '_', $key);
            $helpKey = 'form.help.php_'.$key;
            $fieldOptions = [
                'required' => false,
                'data' => $value,
                'label' => 'forms.php.label.'.$key,
                'help' => $helpKey,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => $key,
                ],
            ];
            if ($key === 'mail.force_extra_parameters') {
                $fieldOptions['disabled'] = true;
            }

            $formBuilder->add($formFieldName, TextType::class, $fieldOptions);
        }

        $formBuilder
            ->add('save', ButtonType::class, [
                'label' => 'Save',
                'icon_before' => 'check',
                'button_class' => 'bg-gradient-to-r from-green-400 to-green-600 text-white font-bold py-2 px-4 rounded',
                'icon_class' => 'w-8 h-8 fill-white button-icon',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
