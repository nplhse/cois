<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\UploadType;
use App\Message\ImportDataMessage;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Service\FileUploader;
use App\Service\RequestParamService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/import")
 * @IsGranted("ROLE_USER")
 */
class ImportController extends AbstractController
{
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
            $import->setUser($user);

            if ($user->getHospital()) {
                $import->setHospital($user->getHospital());
            }

            $import->setStatus('pending');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($import);
            $entityManager->flush();

            $this->dispatchMessage(new ImportDataMessage($import, $hospital));

            return $this->redirectToRoute('app_import_index');
        }

        return $this->render('import/new.html.twig', [
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

        return $this->redirectToRoute('app_import_index');
    }
}
