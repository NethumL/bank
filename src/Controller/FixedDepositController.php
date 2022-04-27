<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FixedDepositController extends AbstractController
{
    #[Route('/fixed-deposit', name: 'app_fixed_deposit')]
    public function index(): Response
    {
        return $this->render('fixed_deposit/index.html.twig', [
            'controller_name' => 'FixedDepositController',
        ]);
    }
}
