<?php

namespace App\Service\Import\Reader;

use App\Application\Contract\ImportReaderInterface;
use League\Csv\Reader;
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
            'hasFieldNames' => false,
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
        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        if (file_exists($path)) {
            $data = file_get_contents($path);
        } else {
            throw new FileNotFoundException($path);
        }

        if (!mb_check_encoding($data, 'UTF-8')) {
            mb_convert_encoding(...[&$data, 'HTML-ENTITIES', 'UTF-8']);
        }

        $reader = Reader::createFromString($data);
        $reader->setDelimiter($this->options['delimiter']);
        $reader->setEnclosure($this->options['enclosure']);

        if ($this->options['hasFieldNames']) {
            $reader->setHeaderOffset(0);
        }

        $records = $reader->getRecords();
        foreach ($records as $offset => $record) {
            yield $record;
        }
    }
}
