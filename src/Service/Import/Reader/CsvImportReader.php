<?php

declare(strict_types=1);

namespace App\Service\Import\Reader;

use App\Application\Contract\ImportReaderInterface;
use League\Csv\Reader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvImportReader implements ImportReaderInterface
{
    public const File_Type = 'text/csv';

    public const File_Alias = [
        'text/plain',
    ];

    private array $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'hasFieldNames' => true,
            'useFieldNames' => true,
            'delimiter' => ';',
            'enclosure' => '"',
        ]);
    }

    public static function getFileType(): string
    {
        return self::File_Type;
    }

    public function getAlias(): ?array
    {
        return self::File_Alias;
    }

    public function importData(string $path): iterable
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($path)) {
            throw new FileNotFoundException($path);
        }

        $csv = Reader::createFromString(file_get_contents($path));
        $csv->setDelimiter($this->options['delimiter']);
        $csv->setEnclosure($this->options['enclosure']);
        $csv->setOutputBOM(Reader::BOM_UTF8);
        $csv->addStreamFilter('convert.iconv.ISO-8859-1/UTF-8');

        $grab = [];
        $columns = $this->getSupportedColumns();

        foreach ($csv->fetchOne() as $index => $column) {
            if (in_array($column, $columns)) {
                if (isset($grab[$column])) {
                    $grab[$column.'2'] = $index;
                    continue;
                }
                $grab[$column] = $index;
            }
        }

        foreach ($csv->getRecords() as $i => $row) {
            if (0 == $i) {
                continue;
            }

            $filteredRow = [];
            foreach ($grab as $column => $index) {
                $filteredRow[$column] = $row[$index];
            }

            yield $filteredRow;
        }
    }

    private function getSupportedColumns(): array
    {
        return [
            'Datum (Eintreffzeit)',
            'Uhrzeit (Eintreffzeit)',
            'Schockraum',
            'Herzkatheter',
            'Anlass',
            'Arbeits-/Wege-/Schulunfall',
            'Geschlecht',
            'Alter',
            'Reanimation',
            'Beatmet',
            'Schock',
            'Ansteckungsfähig',
            'Schwanger',
            'Arztbegleitet',
            'Grund',
            'Transportmittel',
            'Arztbegleitung notwendig',
            'Sekundäranlass',
            'Fachgebiet',
            'Fachbereich',
            'Patienten-Übergabepunkt (PüP)',
            'Fachbereich war abgemeldet?',
            'PZC',
            'PZC-Text',
            'Neben-PZC',
            'Neben-PZC-Text',
            'Datum (Erstellungsdatum)',
            'Uhrzeit (Erstellungsdatum)',
        ];
    }
}
