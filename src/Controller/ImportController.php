<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\ImportType;
use App\Message\ImportDataMessage;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class ImportController extends AbstractController
{
    /**
     * @Route("/import", name="import")
     */
    public function index(Request $request, FileUploader $fileUploader, ImportRepository $importRepository, HospitalRepository $hospitalRepository): Response
    {
        $import = new Import();
        $user = $this->getUser();

        $hospital = $hospitalRepository->findOneByUser($user);

        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $import->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($import);
            $entityManager->flush();

            $this->dispatchMessage(new ImportDataMessage($import, $hospital));
        }

        $imports = $importRepository->findByUser($user);

        return $this->render('import/form.html.twig', [
            'form' => $form->createView(),
            'imports' => $imports,
        ]);
    }
}
