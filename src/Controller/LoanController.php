<?php

namespace App\Controller;

use App\Entity\OnlineLoan;
use App\Entity\User;
use App\Form\OnlineLoanType;
use App\Repository\FdRepository;
use App\Repository\LoanRepository;
use App\Util\MoneyUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoanController extends AbstractController
{
    #[Route('/loan/online', name: 'app_loan_online', methods: ['GET', 'POST'])]
    public function online(
        Request $request,
        FdRepository $fdRepository,
        LoanRepository $loanRepository,
        MoneyUtils $moneyUtils
    ): Response
    {
        /* @var User $user */
        $user = $this->getUser();
        $onlineLoanObj = new OnlineLoan();
        // calculate the loan eligibility
        $userId = $user->getId();
        $fdList = $fdRepository->findByUser($userId);
        $onlineLoanList = $loanRepository->findOnlineLoansByUser($userId);
        $eligibleFdList = [];

        foreach ($fdList as $idx => $fd) {
            $fdId = $fd['ID'];
            $loanBorrowed = false;
            foreach ($onlineLoanList as $onlineLoan) {
                if ($fdId === $onlineLoan['FD_ID'] and $onlineLoan['Status'] !== 'PAID') {
                    $loanBorrowed = true;
                    array_splice($onlineLoanList, $idx, 1);
                    break;
                }
            }
            if (!$loanBorrowed) {
                $fdAmount = $fd['Amount'];
                $fdFraction = $moneyUtils->parseString($fdAmount)->multiply('0.6');
                $onlineLoanUpperBound = $moneyUtils->parseString('500000.00');
                $allowableAmount = $moneyUtils->format(
                                    $onlineLoanUpperBound
                                        ->lessThan($fdFraction) ? $onlineLoanUpperBound : $fdFraction
                                    );
                $eligibleFdList[$fd['ID']] = $allowableAmount;
            }
        }

        $loanEligibility = false;
        if (count($eligibleFdList) > 0) {
            $loanEligibility = true;
        }

        $form = $this->createForm(
            OnlineLoanType::class,
            $onlineLoanObj,
            [
                'loanEligibility' => $loanEligibility,
                'eligibleFdList' => $eligibleFdList
            ]
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
