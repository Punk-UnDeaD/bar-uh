<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function(ContainerConfigurator $di): void {
    $services = $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__.'\\', '.')
        ->exclude('./{Entity,di.php}');

    $services->set('redis', \Redis::class)
        ->call('connect', ['redis']);

    $services->set('lockStore')
        ->class(\Symfony\Component\Lock\Store\RedisStore::class)
        ->args([service('redis')]);

    $services->set('lockFactory')
        ->class(\Symfony\Component\Lock\LockFactory::class)
        ->args([service('lockStore')]);

    $services->set(\Twig\DeferredExtension\DeferredExtension::class);

    $services->set('transliterator.any_to_latin', \Transliterator::class)
        ->factory([\Transliterator::class, 'create'])
        ->args(['Any-Latin']);

    $services->set('transliterator.latin_to_ascii', \Transliterator::class)
        ->factory([\Transliterator::class, 'create'])
        ->args(['Latin-ASCII']);

    $services->set(Service\Transliterator::class)
        ->args([service('transliterator.any_to_latin'), service('transliterator.latin_to_ascii')]);

    $di->extension(
        'framework',
        [
            'messenger' => [
                'routing' => [
                    AsyncEvent\Dispatcher\Message\Message::class         => 'async',
                    AsyncEvent\Dispatcher\Message\DebounceMessage::class => 'async',
                    Middleware\AsyncWrapper\Message::class               => 'async',
                ],
                'buses'   => [
                    'messenger.bus.default' => [
                        'middleware' => [
                            Middleware\OneTimeValidationMiddleware::class,
                            Middleware\AsyncWrapper\WrapperMiddleware::class,
                            Middleware\FlushMiddleware::class,
                            'messenger.middleware.doctrine_transaction',
                        ],
                    ],
                ],
            ],
        ]
    );
};
