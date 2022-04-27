<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoanController extends AbstractController
{
    #[Route('/loan/online', name: 'app_loan_online')]
    public function online(): Response
    {
        return $this->render('loan/online.html.twig', [
            'controller_name' => 'LoanController',
        ]);
    }

    #[Route('/loan/request', name: 'app_loan_request')]
    public function request(): Response
    {
        return $this->render('loan/request.html.twig', [
            'controller_name' => 'LoanController',
        ]);
    }

    #[Route('/loan/approval', name: 'app_loan_approval')]
    public function approval(): Response
    {
        return $this->render('loan/approval.html.twig', [
            'controller_name' => 'LoanController',
        ]);
    }
}
