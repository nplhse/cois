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

        // Ensure detecting of line endings on Mac OS X
        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $content = file_get_contents($path);

        // Enforce correct encoding if content already is UTF-8
        if (mb_detect_encoding($content, 'UTF-8', true)) {
            $content = utf8_decode($content);
        }

        $csv = Reader::createFromString($content);
        $csv->setDelimiter($this->options['delimiter']);
        $csv->setEnclosure($this->options['enclosure']);

        // Add StreamFilter to enforce correct encoding for files created on Microsoft Windows
        if (mb_detect_encoding($content, 'ISO-8859-1', true)) {
            $csv->setOutputBOM(Reader::BOM_UTF8);
            $csv->addStreamFilter('convert.iconv.ISO-8859-1/UTF-8');
        }

        // Filter columns from CSV header (aka first row)
        $filteredColumns = $this->getFilteredColumns($csv->fetchOne());

        // Filter rows before adding content to the iterator
        foreach ($csv->getRecords() as $i => $row) {
            // Ignore CSV header (aka first row)
            if (0 === $i) {
                continue;
            }

            $filteredRow = [];
            foreach ($filteredColumns as $column => $index) {
                $filteredRow[$column] = $row[$index];
            }

            yield $filteredRow;
        }
    }

    public function getFilteredColumns(array $firstRow): array
    {
        $filteredColumns = [];
        $supportedColumns = $this->getSupportedColumns();

        // If column is already set, add a number to it
        foreach ($firstRow as $index => $column) {
            if (in_array($column, $supportedColumns, true)) {
                if (isset($filteredColumns[$column])) {
                    $filteredColumns[$column.'2'] = $index;
                    continue;
                }
                $filteredColumns[$column] = $index;
            }
        }

        return $filteredColumns;
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
