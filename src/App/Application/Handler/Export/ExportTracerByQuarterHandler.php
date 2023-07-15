<?php

declare(strict_types=1);

namespace App\Application\Handler\Export;

use App\Application\Export\DGINATracerByQuarterExport;
use App\Application\Traits\EventDispatcherTrait;
use App\Query\Export\AllocationByQuarterQuery;
use Domain\Command\Export\ExportTracerByQuarterCommand;
use League\Csv\Writer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExportTracerByQuarterHandler
{
    use EventDispatcherTrait;

    private array $data = [];

    public function __construct(
        private AllocationByQuarterQuery $query,
        private string $projectDir
    ) {
    }

    public function __invoke(ExportTracerByQuarterCommand $command): void
    {
        $export = new DGINATracerByQuarterExport($this->projectDir);

        if ($export->fileExists($export->getName())) {
            return;
        }

        $this->data['total'] = $this->query->new()
            ->findAllocationsByQuarter()
            ->getResults();

        $this->data['cpr'] = $this->query->new()
            ->findAllocationsByQuarter()
            ->filterByTracer('cpr')
            ->getResults();

        $this->data['pulmonary_embolism'] = $this->query->new()
            ->findAllocationsByQuarter()
            ->filterByTracer('pulmonary_embolism')
            ->getResults();

        $this->data['acs_stemi'] = $this->query->new()
            ->findAllocationsByQuarter()
            ->filterByTracer('acs_stemi')
            ->getResults();

        $this->data['pneumonia_copd'] = $this->query->new()
            ->findAllocationsByQuarter()
            ->filterByTracer('pneumonia_copd')
            ->getResults();

        $this->data['stroke'] = $this->query->new()
            ->findAllocationsByQuarter()
            ->filterByTracer('stroke')
            ->getResults();

        foreach ($this->data as $key => $row) {
            $tmp = [];

            for ($i = 0, $iMax = count($row); $i < $iMax; ++$i) {
                $tmp[$row[$i]['year'].'-'.$row[$i]['quarter']] = $row[$i]['value'];
            }

            $arr = array_reverse($tmp, true);
            $arr['quarter'] = $key;

            $this->data[$key] = array_reverse($arr, true);
        }

        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $csv = Writer::createFromPath($export->buildPath(), 'w');
        $csv->setDelimiter(';');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        $csv->insertOne(array_keys($this->data['total']));
        $csv->insertAll($this->data);
    }
}
