<?php

namespace App\Service\Import\Reader;

use App\Application\Contract\ImportReaderInterface;
use ForceUTF8\Encoding;
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

    private array $keys;

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

        if ($filesystem->exists($path)) {
            $file = fopen($path, 'r');
        } else {
            throw new FileNotFoundException($path);
        }

        if ($this->options['hasFieldNames']) {
            $this->keys = fgetcsv($file, 0, $this->options['delimiter'], $this->options['enclosure']);
        } else {
            $this->keys = [];
        }

        $result = [];

        while ($row = fgetcsv($file, 0, $this->options['delimiter'], $this->options['enclosure'])) {
            $n = count($row);
            $res = [];
            for ($i = 0; $i < $n; ++$i) {
                $idx = ($this->options['useFieldNames']) ? $this->keys[$i] : $i;
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
