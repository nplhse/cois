<?php

declare(strict_types=1);

use Symfony\Config\Framework\AssetsConfig;

return static function (AssetsConfig $assetsConfig): void {
    $assetsConfig->jsonManifestPath('%kernel.project_dir%/public/build/manifest.json');
};
