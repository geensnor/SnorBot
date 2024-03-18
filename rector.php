<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/',
    ])
    ->withSkip([
        __DIR__.'/vendor',
    ])
    ->withPreparedSets(typeDeclarations: true)
    ->withDeadCodeLevel(40)
    ->withPhpSets(php82: true);
