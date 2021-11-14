<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\ImportType;
use App\Form\UploadType;
use App\Message\ImportDataMessage;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Service\AdminNotificationService;
use App\Service\FileUploader;
use App\Service\RequestParamService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/import")
 * @IsGranted("ROLE_USER")
 */
class ImportController extends AbstractController
{
    private AdminNotificationService $adminNotifier;

    public function __construct(AdminNotificationService $adminNotifier)
    {
        $this->adminNotifier = $adminNotifier;
    }

    /**
     * @Route("/", name="app_import_index")
     */
    public function index(Request $request, FileUploader $fileUploader, ImportRepository $importRepository, HospitalRepository $hospitalRepository): Response
    {
        $hospital = $hospitalRepository->findOneBy(['owner' => $this->getUser()->getId()]);

        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();
        $filters['show'] = $paramService->getShow();

        $filters['user'] = $this->getUser();
        $filters['hospital'] = $this->getUser()->getHospital();

        $filters['sortBy'] = $paramService->getSortBy();
        $filters['orderBy'] = $paramService->getOrderBy();

        $paginator = $importRepository->getImportPaginator($paramService->getPage(), $filters);

        return $this->render('import/index.html.twig', [
            'imports' => $paginator,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), HospitalRepository::PAGINATOR_PER_PAGE),
            'filters' => $filters,
            'filterIsSet' => $paramService->isFilterIsSet(),
        ]);
    }

    /**
     * @Route("/new", name="app_import_new")
     */
    public function new(Request $request, FileUploader $fileUploader, ImportRepository $importRepository): Response
    {
        $hospital = $this->getUser()->getHospital();

        if (!isset($hospital)) {
            return $this->redirectToRoute('app_import_index');
        }

        $import = new Import();
        $user = $this->getUser();

        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $import = $form->getData();

            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $fileData = $fileUploader->uploadFile($file);

            $import->setName($fileData['uniqueName']);
            $import->setExtension($file->getClientOriginalExtension());
            $import->setPath($fileData['path']);
            $import->setMimeType($file->getMimeType());
            $import->setSize($file->getSize());
            $import->setCreatedAt(new \DateTime('NOW'));
            $import->setIsFixture(false);
            $import->setFile(null);

            $import->setUser($user);
            $import->setHospital($hospital);

            $import->setStatus('pending');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($import);
            $entityManager->flush();

            try {
                $this->dispatchMessage(new ImportDataMessage($import, $hospital));

                $this->addFlash('success', 'Your import was successfully created.');
            } catch (HandlerFailedException $e) {
                $import->setStatus('failed');
                $import->setLastError($e->getMessage());
                $import->setLastRun(new \DateTime('NOW'));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($import);
                $entityManager->flush();

                $this->addFlash('danger', 'Your import failed, see details for more information. We have send a notification to the admin to handle this issue.');

                $this->adminNotifier->sendFailedImportNotification($import);
            }

            return $this->redirectToRoute('app_import_show', ['id' => $import->getId()]);
        }

        return $this->render('import/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_import_edit")
     */
    public function edit(Import $import, Request $request, ImportRepository $importRepository): Response
    {
        $this->denyAccessUnlessGranted('delete', $import);

        $form = $this->createForm(ImportType::class, $import, ['create' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($import);
            $entityManager->flush();

            $this->addFlash('success', 'Your import was successfully updated.');

            return $this->redirectToRoute('app_import_show', ['id' => $import->getId()]);
        }

        return $this->render('import/edit.html.twig', [
            'import' => $import,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_import_show", methods={"GET"})
     */
    public function show(Import $import): Response
    {
        $userIsOwner = $import->getUser() == $this->getUser();

        return $this->render('import/show.html.twig', [
            'import' => $import,
            'user_is_owner' => $userIsOwner,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_import_delete", methods={"POST"})
     */
    public function delete(Import $import, AllocationRepository $allocationRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('delete', $import);

        $allocationRepository->deleteByImport($import);

        $em->remove($import);
        $em->flush();

        $this->addFlash('danger', 'Your import was successfully deleted.');

        return $this->redirectToRoute('app_import_index');
    }
}
