<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Form\OnlineLoanType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoanController extends AbstractController
{
    #[Route('/loan/online', name: 'app_loan_online', methods: ['GET', 'POST'])]
    public function online(Request $request): Response
    {
        $user = $this->getUser();
        $onlineLoan = new Loan();
        // TODO: calculate the loan eligibility
        $loanEligibility = true;

        $form = $this->createForm(
            OnlineLoanType::class,
            $onlineLoan,
            ['loanEligibility' => $loanEligibility]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $onlineLoan = $form->getData();

            // Save data

            return $this->redirectToRoute('app_loan_online');
        }

        return $this->renderForm('loan/online.html.twig', [
            'form' => $form,
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
