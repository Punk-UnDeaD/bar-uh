<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\ResetInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Bridge\Twig\Extension\WebLinkExtension as OriginWebLinkExtension;

#[Autoconfigure(bind: ['$twigExtensionWeblink' => '@twig.extension.weblink'])]
class WebLinkExtension extends AbstractExtension implements ResetInterface
{
    #[Required]
    public RequestStack $requestStack;

    /** @var array<string> */
    private array $fonts = [];

    /** @var array<string> */
    private array $preconnects = [];

    public function __construct(private OriginWebLinkExtension $twigExtensionWeblink)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('addGoogleFontLink', [$this, 'addGoogleFontLink'], ['is_safe' => ['html']]),
            new TwigFunction('addPreconnect', [$this, 'addPreconnect']),
        ];
    }

    public function addGoogleFontLink(string $href): string
    {
        if (in_array($href, $this->fonts)) {
            return '';
        }

        $this->addPreconnect('https://fonts.gstatic.com/');
        $this->fonts[] = $href;

        return "<link rel=\"preload\" href=\"{$href}\" as=\"style\" onload=\"this.rel='stylesheet'\">";
    }

    public function addPreconnect(string $href): void
    {
        if (in_array($href, $this->preconnects)) {
            return;
        }
        $this->twigExtensionWeblink->preconnect($href, ['crossorigin' => true]);
        $this->preconnects[] = $href;
    }

    public function reset(): void
    {
        $this->fonts = [];
        $this->preconnects = [];
    }
}
