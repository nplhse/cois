<?php

namespace App\Controller;

use App\Entity\Import;
use App\Service\UploadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route(path: '/_files')]
class FileController extends AbstractController
{
    #[Route(path: '/alloc/{id}', name: 'app_files_allocations')]
    public function index(Import $import, UploadService $fileUploader): Response
    {
        $path = $import->getFilePath();

        $response = new StreamedResponse(function () use ($path, $fileUploader) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $fileUploader->streamFile($path);
            stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', $import->getFileMimeType());

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $import->getName().'.'.$import->getFileExtension()
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
