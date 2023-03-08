<?php

declare(strict_types=1);

namespace App\Application\Export;

use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractExport
{
    public const EXPORT_DIR = '/var/storage/export/';

    private array $data = [];

    public function buildPath(?string $exportName = null): string
    {
        if (null === $exportName) {
            $exportName = $this->getName();
        }

        return $this->getProjectDir().self::EXPORT_DIR.$exportName.'.csv';
    }

    public function fileExists(string $exportName): bool
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($this->buildPath($exportName))) {
            return true;
        }

        return false;
    }

    abstract public function getName(): string;

    abstract public function getProjectDir(): string;
}
