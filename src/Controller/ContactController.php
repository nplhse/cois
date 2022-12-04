<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use App\Service\Mailers\ContactFormNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private ContactFormNotificationService $mailerService
    ) {
    }

    #[Route('/contact', name: 'app_contact')]
    public function form(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();

            $this->mailerService->sendContactFormNotification($contactFormData);

            return $this->render('contact/sent.html.twig');
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
