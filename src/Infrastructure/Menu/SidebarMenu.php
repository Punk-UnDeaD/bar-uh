<?php

declare(strict_types=1);

namespace App\Infrastructure\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

#[AutoconfigureTag('knp_menu.menu_builder', ['method' => 'build', 'alias' => 'sidebar'])]
class SidebarMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private FactoryInterface $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array<mixed> $options
     *
     * @phpstan-return \Knp\Menu\ItemInterface<?ItemInterface>
     * @psalm-return \Knp\Menu\ItemInterface
     */
    public function build(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes(['class' => 'sidebar-menu']);

        $menu->addChild('Home', ['route' => 'front'])
            ->setExtra('icon', 'home');
        $menu->addChild('Dashboard', ['route' => 'front'])
            ->setExtra('icon', 'dashboard');
        $media = $menu->addChild('Media', ['route' => 'front'])
            ->setExtra('icon', 'embed');
        $media->addChild('Images', ['route' => 'admin.media.image'])
            ->setExtra('icon', 'image');

        return $menu;
    }
}
