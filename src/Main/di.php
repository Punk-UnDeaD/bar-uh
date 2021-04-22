<?php

declare(strict_types=1);

namespace App\Main;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function(ContainerConfigurator $di): void {
    $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__.'\\', '.')
        ->exclude('./{Entity,Command.php,di.php,routing.php}');

    $di->extension(
        'twig',
        [
            'paths' => [
                __DIR__.'/templates' => basename(__DIR__),
            ],
        ]
    );

    $di->extension('knp_menu', ['twig' => [
        'template' => '@Main/_override/menu.html.twig',
    ]]);
};
