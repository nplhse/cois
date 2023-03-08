<?php

namespace App\Twig\Components;

use App\Application\Export\AbstractExport;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('download_export')]
final class DownloadExportComponent
{
    use DefaultActionTrait;

    private AbstractExport $export;

    public function __construct(
        public string $projectDir,
    ) {
        
    }
}
