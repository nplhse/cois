<?php

namespace App\Service\Import;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{
    public function __construct(
        private FileSystemOperator $storage
    ) {
    }

    public function uploadFile(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $extension = preg_replace('/[^\w-]/', '', $extension);

        $path = $this->generateDirectory(time()).$this->generateRandomName($file).'.'.$extension;

        try {
            $this->storage->write($path, $file->getContent());
        } catch (\Exception) {
            throw new \Exception(sprintf('Could not upload file "%s"', $path));
        }

        return $path;
    }

    public function streamFile(string $path): mixed
    {
        $resource = $this->storage->readStream($path);

        if (false == $resource) {
            throw new \Exception(sprintf('Error opening stream for "%s"', $path));
        }

        return $resource;
    }

    private function generateRandomName(UploadedFile $file): string
    {
        return bin2hex(random_bytes(24));
    }

    private function generateDirectory(int $timestamp): string
    {
        return date('Y', $timestamp).'/'.date('m', $timestamp).'/'.date('d', $timestamp).'/';
    }
}
