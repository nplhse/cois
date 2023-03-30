<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use App\Domain\Command\Export\ExportTracerByQuarterCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    #[Route('/export/', name: 'app_export_index')]
    public function index(): Response
    {
        return $this->render('data/export/index.html.twig', [
            'controller_name' => 'ExportController',
        ]);
    }

    #[Route('/export/test', name: 'app_export_index_test')]
    public function test(): Response
    {
        $command = new ExportTracerByQuarterCommand();

        $this->messageBus->dispatch($command);

        return $this->render('data/export/index.html.twig', [
            'controller_name' => 'ExportController',
        ]);
    }
}
