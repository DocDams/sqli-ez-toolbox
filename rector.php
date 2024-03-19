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
        [
            SetList::PHP_71,
            SetList::PHP_72,
            SetList::PHP_73,
            SetList::PHP_74, // types properties
            SetList::PHP_80, // class attributes
            SetList::PHP_81, // named arguments

            ]
    )
    ->withAttributesSets(symfony: true, doctrine: true) // remove it later
    ;
