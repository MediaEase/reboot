<?php

declare(strict_types=1);

return [
    'preset' => 'symfony',
    'ide' => 'vscode',
    'exclude' => [
        'vendor/',
        'tools/',
        'var/',
        'migrations/',
        'phpinsights.php',
    ],

    'add' => [
    ],

    'remove' => [
        // Allow Yoda style (symfony preset)
        SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
    ],

    'config' => [
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff::class => [
            'exclude' => [
                'src/Handler/UserHandlerInterface.php',
                'src/Handler/TaskHandlerInterface.php',
            ],
        ],
        SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class => [
            'exclude' => [
                'src/Security',
                'migrations/',
            ],
        ],
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class => [
            'exclude' => [
                'src/Form',
                'src/Security/TaskVoter.php',
            ],
        ],
        NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class => [
            'exclude' => [
                'src/Entity',
            ],
        ],
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class => [
            'exclude' => [
                'src/Entity',
            ],
        ],
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 160,
            'ignoreComments' => true,
        ],
        SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff::class => [
            'traversableTypeHints' => [],
        ],
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff::class => [
            'exclude' => [
                'src/Metrics',
            ],
        ],
        NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class => [
            'exclude' => [
                'src/DataFixtures/',
                'src/Entity/',
            ],
        ],
    ],

    'requirements' => [
        'min-quality' => 95,
        'min-complexity' => 75,
        'min-architecture' => 95,
        'min-style' => 95,
    ],

    'threads' => 4,
];
