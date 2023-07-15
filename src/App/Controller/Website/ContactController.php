<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Domain\Enum\ContactRequestStatus;
use App\Entity\ContactRequest;
use App\Form\ContactType;
use App\Repository\ContactRequestRepository;
use App\Service\Mailers\ContactFormNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private ContactRequestRepository $contactRequestRepository,
        private ContactFormNotificationService $mailerService
    ) {
    }

    #[Route('/contact', name: 'app_contact')]
    public function form(Request $request): Response
    {
        $contactRequest = new ContactRequest();

        $form = $this->createForm(ContactType::class, $contactRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRequest->setCreatedAt(new \DateTimeImmutable());
            $contactRequest->setStatus(ContactRequestStatus::OPEN);
            $this->contactRequestRepository->save($contactRequest, true);

            $this->mailerService->sendContactFormNotification($contactRequest);

            return $this->redirectToRoute('app_contact_sent');
        }

        return $this->render('website/contact/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/contact/sent', name: 'app_contact_sent')]
    public function sent(): Response
    {
        return $this->render('website/contact/sent.html.twig');
    }
}
