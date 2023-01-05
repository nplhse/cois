<?php

declare(strict_types=1);

namespace App\Controller\Import;

use App\Entity\Import;
use App\Service\Import\UploadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[IsGranted('ROLE_ADMIN')]
class ImportFileController extends AbstractController
{
    #[Route(path: '/import/{id}/_file', name: 'app_file_import')]
    public function index(Import $import, UploadService $fileUploader): Response
    {
        $path = $import->getFilePath();

        $response = new StreamedResponse(function () use ($path, $fileUploader): void {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $fileUploader->streamFile($path);
            stream_copy_to_stream($fileStream, $outputStream);
        });

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $this->getSafeFilename($import->getName(), $import->getFileExtension()),
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $import->getFileMimeType());

        return $response;
    }

    private function getSafeFilename(string $name, string $extension): string
    {
        $slugger = new AsciiSlugger();

        return $slugger->slug($name).'.'.$extension;
    }
}
