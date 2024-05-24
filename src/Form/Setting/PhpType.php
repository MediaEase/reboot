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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Builds a form for PHP INI settings, grouped by their respective sections.
 */
final class PhpType extends AbstractType
{
    /**
     * Builds the form with grouped settings.
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param array                $options     Options passed to the form
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        foreach ($options['data'] as $section => $settings) {
            $sectionForm = $formBuilder->create($section, FormType::class, ['label' => false]);
            foreach ($settings as $key => $value) {
                $formFieldName = str_replace('.', '_', $key);
                $fieldOptions = [
                    'required' => false,
                    'data' => $value,
                    'label' => 'forms.php.label.'.$key,
                    'attr' => [
                        'class' => 'input',
                        'placeholder' => $key,
                    ],
                ];
                if ($key === 'mail.force_extra_parameters') {
                    $fieldOptions['disabled'] = true;
                }

                $sectionForm->add($formFieldName, TextType::class, $fieldOptions);
            }

            $formBuilder->add($sectionForm);
        }

        $formBuilder->add('save', ButtonType::class, [
            'label' => 'Save',
            'icon_before' => 'flowbite:floppy-disk-outline',
            'button_class' => 'bg-gradient-to-r from-green-400 to-green-600 text-white font-bold py-2 px-4 rounded',
            'icon_class' => 'w-8 h-8 fill-white button-icon',
        ]);
    }

    /**
     * Configures options for this form type.
     *
     * @param OptionsResolver $optionsResolver The resolver for options
     */
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => null,
            'data' => [],
        ]);
    }
}
