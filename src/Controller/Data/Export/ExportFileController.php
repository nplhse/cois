<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ExportFileController extends AbstractController
{
    public function __construct(
        #[TaggedIterator('app.export')]
        private readonly iterable $exports,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[Route(path: '/data/export/{target}', name: 'app_file_export')]
    public function __invoke(string $target): Response
    {
        $activeExport = $this->selectExport($target);

        if (!$activeExport->fileExists($target)) {
            $name = $activeExport->getCommand();
            $command = new $name();
            $this->messageBus->dispatch($command);

            return $this->render('data/export/file_creating.html.twig');
        }

        $file = $activeExport->buildPath($target);

        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', 'text/csv');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $activeExport->getName().'.csv',
        );

        return $response;
    }

    private function selectExport(string $target): object
    {
        foreach ($this->exports as $export) {
            if ($export->getName() === $target) {
                return $export;
            }
        }

        throw new \Exception('No Export was selected');
    }
}
