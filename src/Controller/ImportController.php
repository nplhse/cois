<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\ImportType;
use App\Message\ImportDataMessage;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Service\FileUploader;
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
     * @Route("/", name="import_index")
     */
    public function index(Request $request, FileUploader $fileUploader, ImportRepository $importRepository, HospitalRepository $hospitalRepository): Response
    {
        $hospital = $hospitalRepository->findOneBy(['owner' => $this->getUser()->getId()]);

        if (!$hospital) {
            $this->addFlash('danger', 'You been redirected, because you have to be owner of a hospital in order to access this page.');

            return $this->redirectToRoute('allocation_index');
        }

        $import = new Import();

        $form = $this->createForm(ImportType::class);
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
            $import->setUser($this->getUser());
            $import->setStatus('pending');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($import);
            $entityManager->flush();

            $this->dispatchMessage(new ImportDataMessage($import, $hospital));
        }

        $imports = $importRepository->findByUser($this->getUser());

        return $this->render('import/form.html.twig', [
            'form' => $form->createView(),
            'imports' => $imports,
        ]);
    }

    /**
     * @Route("/{id}", name="import_show", methods={"GET"})
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
     * @Route("/{id}/delete", name="import_delete", methods={"GET"})
     */
    public function delete(Import $import, AllocationRepository $allocationRepository, EntityManagerInterface $em): Response
    {
        $userIsOwner = $import->getUser() == $this->getUser();

        if (!$userIsOwner) {
            return $this->redirectToRoute('import_index');
        }

        $allocationRepository->deleteByImport($import);

        $em->remove($import);
        $em->flush();

        return $this->redirectToRoute('import_index');
    }
}
