<?php

declare(strict_types=1);

namespace App\Application\Handler\Export;

use App\Application\Export\DGINAUsageByTimesExport;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Export\ExportUsageByTimesCommand;
use App\Query\Export\AllocationByHourQuery;
use League\Csv\Writer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExportUsageByTimesHandler
{
    use EventDispatcherTrait;

    private array $data = [];

    public function __construct(
        private AllocationByHourQuery $query,
        private string $projectDir
    ) {
    }

    public function __invoke(ExportUsageByTimesCommand $command): void
    {
        $export = new DGINAUsageByTimesExport($this->projectDir);

        if ($export->fileExists($export->getName())) {
            return;
        }

        $this->data['total'] = $this->query->new()
            ->findAllocationsByHour()
            ->getResults();

        $this->data['int'] = $this->query->new()
            ->findAllocationsByHour()
            ->filterBySpeciality('Innere Medizin')
            ->getResults();

        $this->data['int_cathlab'] = $this->query->new()
            ->findAllocationsByHour()
            ->filterBySpeciality('Innere Medizin')
            ->filterByProperty('cathlab')
            ->getResults();

        $this->data['trauma'] = $this->query->new()
            ->findAllocationsByHour()
            ->filterBySpecialityDetail('Unfallchirurgie')
            ->getResults();

        $this->data['trauma_resus'] = $this->query->new()
            ->findAllocationsByHour()
            ->filterBySpecialityDetail('Unfallchirurgie')
            ->filterByProperty('resus')
            ->getResults();

        $this->data['neuro'] = $this->query->new()
            ->findAllocationsByHour()
            ->filterBySpeciality('Neurologie')
            ->getResults();

        $this->data['neuro_resus'] = $this->query->new()
            ->findAllocationsByHour()
            ->filterBySpeciality('Neurologie')
            ->filterByProperty('resus')
            ->getResults();

        $this->data['peds'] = $this->query->new()
            ->findAllocationsByHour()
            ->filterBySpeciality('Kinder- und Jugendmedizin')
            ->getResults();

        $this->data['urology'] = $this->query->new()
            ->findAllocationsByHour()
            ->filterBySpeciality('Urologie')
            ->getResults();

        foreach ($this->data as $key => $row) {
            $tmp = [];

            for ($i = 0; $i <= 23; ++$i) {
                $tmp[$i] = 0;
            }

            for ($i = 0, $iMax = count($row); $i < $iMax; ++$i) {
                $tmp[$row[$i]['hour']] = $row[$i]['value'];
            }

            array_unshift($tmp, $key);
            $this->data[$key] = $tmp;
        }

        array_unshift($this->data, ['hour', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23]);

        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $csv = Writer::createFromPath($export->buildPath(), 'w');
        $csv->setDelimiter(';');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        $csv->insertAll($this->data);
    }
}
