<?php

declare(strict_types=1);

namespace App\Infrastructure\ViteAsset;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ViteAssetExtension extends AbstractExtension
{
    public final const CACHE_KEY = 'vite_manifest';
    private ?array $manifestData = null;

    public function __construct(
        private readonly bool $isDev,
        private readonly string $manifest,
        private readonly CacheItemPoolInterface $cache,
        private readonly RequestStack $requestStack
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_asset', [$this, 'asset'], ['is_safe' => ['html']]),
        ];
    }

    public function asset(string $entry, array $deps = []): string
    {
        if ($this->isDev) {
            return $this->assetDev($entry, $deps);
        }

        return $this->assetProd($entry);
    }

    public function assetDev(string $entry, array $deps): string
    {
        $html = <<<'HTML'
            <script type="module" src="http://localhost:3000/build/@vite/client"  data-turbo-track="reload"></script>
            HTML;
        if (\in_array('react', $deps, true)) {
            $html .= '<script type="module"  data-turbo-track="reload">
                import RefreshRuntime from "http://localhost:3000/build/@react-refresh"
    RefreshRuntime.injectIntoGlobalHook(window)
    window.$RefreshReg$ = () => {}
    window.$RefreshSig$ = () => (type) => type
    window.__vite_plugin_react_preamble_installed__ = true
        </script>';
        }
        $html .= <<<HTML
            <script type="module" src="http://localhost:3000/build/{$entry}"  data-turbo-track="reload" defer></script>
            HTML;
        $host = $this->requestStack->getCurrentRequest()?->getHost();

        return $host === null ? $html : str_replace('localhost', $host, $html);
    }

    public function assetProd(string $entry): string
    {
        if ($this->manifestData === null) {
            $item = $this->cache->getItem(self::CACHE_KEY);
            if ($item->isHit()) {
                $this->manifestData = $item->get();
            } else {
                $this->manifestData = json_decode((string) file_get_contents($this->manifest), true);
                $item->set($this->manifestData);
                $this->cache->save($item);
            }
        }
        $file = $this->manifestData[$entry]['file'];
        $css = $this->manifestData[$entry]['css'] ?? [];
        $imports = $this->manifestData[$entry]['imports'] ?? [];
        $html = <<<HTML
            <script type="module" src="/build/{$file}" data-turbo-track="reload" defer></script>
            HTML;
        foreach ($css as $cssFile) {
            $html .= <<<HTML
                <link rel="stylesheet" media="screen" href="/build/{$cssFile}"  data-turbo-track="reload"/>
                HTML;
        }

        foreach ($imports as $import) {
            if (\array_key_exists($import, $this->manifestData)) {
                $html .= $this->assetProd($import);
            } else {
                $html .= <<<HTML
                    <link rel="modulepreload" href="/build/{$import}"  data-turbo-track="reload"/>
                    HTML;
            }
        }

        return $html;
    }
}
