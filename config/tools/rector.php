<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\CodeQuality\Rector\Foreach_\ForeachItemsAssignToEmptyArrayToAssignRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return static function (RectorConfig $rectorConfig): void {
    // get root dir
    $srcDir = dirname(__DIR__, 2).'/src';
    $testsDir = dirname(__DIR__, 2).'/tests';
    $entityDir = $srcDir.'/Entity';
    if (! is_dir($srcDir) || ! is_dir($testsDir)) {
        throw new \Exception(
            "Directories do not exist: $srcDir or $testsDir \n"
        );
    }
    $rectorConfig->paths(
        [$srcDir, $testsDir]
    );

    $rectorConfig->sets([
        SetList::DEAD_CODE,
        SetList::PHP_80,
        SetList::STRICT_BOOLEANS,
        SetList::CODING_STYLE,
        SetList::NAMING,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
        SetList::INSTANCEOF,
        SetList::CODE_QUALITY,
    ]);

    $rectorConfig->skip([ForeachItemsAssignToEmptyArrayToAssignRector::class,RemoveNonExistingVarAnnotationRector::class,$entityDir."/User.php",$entityDir."/Store.php"]);

    $rectorConfig->rule(TypedPropertyFromStrictConstructorRector::class);

    $rectorConfig->phpVersion(PhpVersion::PHP_80);
};
