<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveDeadReturnRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return static function (RectorConfig $rectorConfig): void {
    // Configs
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_84,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
    ]);
    $rectorConfig->phpVersion(PhpVersion::PHP_83);
    $rectorConfig->parallel(600, 4);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);
    $rectorConfig->cacheDirectory(__DIR__.'/.rector');
    $rectorConfig->cacheClass(FileCacheStorage::class);

    // Rules
    $rectorConfig->rule(RemoveAlwaysElseRector::class);
    $rectorConfig->rule(RemoveDeadReturnRector::class);
    $rectorConfig->rule(SimplifyUselessVariableRector::class);
    $rectorConfig->rule(AddVoidReturnTypeWhereNoReturnRector::class);

    $rectorConfig->skip([
        ArgumentAdderRector::class,
        CompleteDynamicPropertiesRector::class,
        IssetOnPropertyObjectToPropertyExistsRector::class,
        NullToStrictStringFuncCallArgRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,
        AddOverrideAttributeToOverriddenMethodsRector::class,
        AddTypeToConstRector::class,
        DisallowedEmptyRuleFixerRector::class,
    ]);

    $rectorConfig->paths([
        __DIR__.'/app/**',
        __DIR__.'/config/**',
        __DIR__.'/tests/**',
        __DIR__.'/database/factories/**',
        __DIR__.'/database/seeders/**',
    ]);
};
