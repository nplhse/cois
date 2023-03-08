<?php

declare(strict_types=1);

namespace App\Application\Export;

class DGINATracerByQuarterExport extends AbstractExport
{
    private string $name = 'dgina_tracer_by_quarter';

    public function __construct(
        private readonly string $projectDir
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }
}
