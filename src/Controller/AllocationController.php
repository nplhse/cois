<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class AllocationController extends AbstractController
{
    /**
     * @Route("/allocation", name="allocation")
     */
    public function index(): Response
    {
        return $this->render('allocation/index.html.twig', [
            'controller_name' => 'AllocationConstroller',
        ]);
    }
}
