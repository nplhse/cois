<?php

namespace App\Controller\Settings;

use App\Entity\Import;
use App\Form\ImportType;
use App\Message\ImportDataMessage;
use App\Repository\ImportRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/import')]
class ImportController extends AbstractController
{
    #[Route('/', name: 'app_settings_import_index', methods: ['GET'])]
    public function index(Request $request, ImportRepository $importRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $filter = [];

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $importRepository->getImportPaginator($offset, $filter);

        return $this->render('settings/import/index.html.twig', [
            'imports' => $paginator,
            'perPage' => ImportRepository::PAGINATOR_PER_PAGE,
            'previous' => $offset - ImportRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ImportRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_import_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $import = $form->getData();

            $fileData = $fileUploader->uploadFile($file);

            $import->setName($fileData['uniqueName']);
            $import->setExtension($file->getClientOriginalExtension());
            $import->setPath($fileData['path']);
            $import->setMimeType($file->getMimeType());
            $import->setSize($file->getSize());
            $import->setCreatedAt(new \DateTime('NOW'));
            $import->setIsFixture(false);
            $import->setFile(null);
            $import->setStatus('pending');

            //$entityManager = $this->getDoctrine()->getManager();
            //$entityManager->persist($import);
            //$entityManager->flush();

            $hospital = null;

            // $this->dispatchMessage(new ImportDataMessage($import, $hospital));

            return $this->redirectToRoute('app_settings_import_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/import/new.html.twig', [
            'import' => $import,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_import_show', methods: ['GET'])]
    public function show(Import $import): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('settings/import/show.html.twig', [
            'import' => $import,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_import_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Import $import): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ImportType::class, $import, ['backend' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_settings_import_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/import/edit.html.twig', [
            'import' => $import,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_import_delete', methods: ['POST'])]
    public function delete(Request $request, Import $import): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$import->getId(), $CsrfToken)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($import);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_settings_import_index', [], Response::HTTP_SEE_OTHER);
    }
}
