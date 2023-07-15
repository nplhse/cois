<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetReadExtension extends AbstractExtension
{
    public function __construct(
        private Packages $assetsManager
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_read', [$this, 'assetRead']),
        ];
    }

    public function assetRead(string $uri, string $package = null): string
    {
        $file = __DIR__.'/../../public'.$this->assetsManager->getUrl($uri, $package);

        if (is_file($file)) {
            return file_get_contents($file);
        }

        throw new \Twig\Error\Error('File "'.$file.'" not found.');
    }
}
