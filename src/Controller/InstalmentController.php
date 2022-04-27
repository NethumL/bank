<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstalmentController extends AbstractController
{
    #[Route('/instalment/view', name: 'app_instalment_view')]
    public function view(): Response
    {
        return $this->render('instalment/view.html.twig', [
            'controller_name' => 'InstalmentController',
        ]);
    }

    #[Route('/instalment/pay', name: 'app_instalment_pay')]
    public function pay(): Response
    {
        return $this->render('instalment/pay.html.twig', [
            'controller_name' => 'InstalmentController',
        ]);
    }
}
