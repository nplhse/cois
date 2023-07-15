<?php

declare(strict_types=1);

namespace App\Application\Export;

use Domain\Command\Export\ExportUsageByTimesCommand;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.export')]
class DGINAUsageByTimesExport extends AbstractExport
{
    private string $name = 'dgina_usage_by_times';

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

    public function getCommand(): string
    {
        return ExportUsageByTimesCommand::class;
    }
}
