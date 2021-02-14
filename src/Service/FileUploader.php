<?php

namespace App\Service;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private FilesystemOperator $storage;

    public function __construct(FileSystemOperator $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }

    /**
     * @return array<string>
     *
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file): array
    {
        $uniqueName = $this->generateRandomName($file);

        $time = time();
        $dir = date('Y', $time).'/'.date('m', $time).'/'.date('d', time()).'/';

        $extension = $file->getClientOriginalExtension();
        $extension = preg_replace('/[^\w-]/', '', $extension);

        $path = $dir.$uniqueName.'.'.$extension;

        try {
            $this->storage->write($path, $file->getContent());
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $uniqueName));
        }

        return [
            'file' => $file,
            'uniqueName' => $uniqueName,
            'path' => $path,
        ];
    }

    private function generateRandomName(UploadedFile $file): string
    {
        return bin2hex(random_bytes(24));
    }
}
