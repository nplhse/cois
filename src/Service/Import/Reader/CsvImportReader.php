<?php

namespace App\Service\Import\Reader;

use App\Application\Contract\ImportReaderInterface;
use ForceUTF8\Encoding;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class CsvImportReader implements ImportReaderInterface
{
    public const File_Type = 'text/csv';

    public const File_Alias = [
        'text/plain',
    ];

    private array $defaults = [
        'hasFieldNames' => true,
        'delimiter' => ';',
        'enclosure' => '"',
    ];

    public static function getFileType(): string
    {
        return self::File_Type;
    }

    public function getAlias(): ?array
    {
        return self::File_Alias;
    }

    public function setDefaults(array $defaults): void
    {
        $this->defaults = array_merge($this->defaults, $defaults);
    }

    public function importData(string $path): iterable
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($path)) {
            $file = fopen($path, 'r');
        } else {
            throw new FileNotFoundException($path);
        }

        if ($this->defaults['hasFieldNames']) {
            $keys = fgetcsv($file, 0, $this->defaults['delimiter'], $this->defaults['enclosure']);
        } else {
            $keys = [];
        }

        $result = [];

        while ($row = fgetcsv($file, 0, $this->defaults['delimiter'], $this->defaults['enclosure'])) {
            $n = count($row);
            $res = [];
            for ($i = 0; $i < $n; ++$i) {
                $idx = ($this->defaults['hasFieldNames']) ? $keys[$i] : $i;
                $id8 = Encoding::fixUTF8($idx);
                $val = Encoding::fixUTF8($row[$i]);

                // Fixing inconsistent naming of Schockraum in Marburg-Biedenkopf
                if ('Schockraum' === $id8 && isset($res['Schockraum'])) {
                    $id8 = 'Schockraum Art';
                }

                $res[$id8] = $val;
            }

            yield $res;
        }

        fclose($file);
    }
}
