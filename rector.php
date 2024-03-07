<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Annotations',
        __DIR__ . '/Attributes',
        __DIR__ . '/Services',
        __DIR__ . '/tests',
    ])


    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
    ])
    ->withSets(
        [SetList::PHP_81,

            ]
    )
    ->withAttributesSets(symfony: true, doctrine: true) // remove it later
    ;
