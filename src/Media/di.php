<?php

declare(strict_types=1);

namespace App\Media;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function(ContainerConfigurator $di): void {
    $di->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$cacheStorageLifeTime', 900)
        ->load(__NAMESPACE__.'\\', '.')
        ->exclude('./{Entity,Command.php,di.php,routing.php}');

    $di->extension(
        'doctrine',
        [
            'dbal' => [
                'types' => [
                    Entity\DataType\ImageInfoType::NAME => Entity\DataType\ImageInfoType::class,
                ],
            ],
            'orm'  => [
                'mappings' => [
                    __NAMESPACE__ => [
                        'is_bundle' => false,
                        'type'      => 'attribute',
                        'dir'       => __DIR__.'/Entity',
                        'prefix'    => __NAMESPACE__.'\Entity',
                        'alias'     => basename(__DIR__),
                    ],
                ],
            ],
        ]
    );

    $di->extension(
        'twig',
        [
            'paths' => [
                __DIR__.'/templates' => basename(__DIR__),
            ],
        ]
    );
};
