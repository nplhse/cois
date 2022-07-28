<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetReadExtension extends AbstractExtension
{
    private $assetsManager;

    public function __construct(\Symfony\Component\Asset\Packages $assetsManager)
    {
        $this->assetsManager = $assetsManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('asset_read', [$this, 'assetRead']),
        ];
    }

    public function assetRead($uri, $package = null)
    {
        $file = __DIR__.'/../../public'.$this->assetsManager->getUrl($uri, $package);

        if (is_file($file)) {
            return file_get_contents($file);
        }

        throw new \Twig\Error\Error('File "'.$file.'" not found.');
    }
}
