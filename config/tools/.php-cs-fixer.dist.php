<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('tools')
    ->exclude('public')
    ->in(dirname(__DIR__, 3))
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'strict_param' => true,
        'yoda_style' => false,
        'nullable_type_declaration_for_default_null_value' => true,
        'unary_operator_spaces' => false,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;
