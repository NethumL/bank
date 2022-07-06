<?php

namespace App\Controller;

use App\Entity\InstalmentSet;
use App\Entity\Loan;
use App\Entity\NormalLoan;
use App\Entity\OnlineLoan;
use App\Entity\User;
use App\Form\LoanRequestType;
use App\Form\OnlineLoanType;
use App\Repository\FdRepository;
use App\Repository\InstalmentRepository;
use App\Repository\LoanRepository;
use App\Repository\UserRepository;
use App\Util\MoneyUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class LoanController extends AbstractController
{
    #[Route('/loan/online', name: 'app_loan_online', methods: ['GET', 'POST'])]
    public function online(
        Request $request,
        FdRepository $fdRepository,
        LoanRepository $loanRepository,
        InstalmentRepository $instalmentRepository,
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
        $loanPlans = $loanRepository->getLoanPlans();

        foreach ($fdList as $idx => $fd) {
            if ($fdRepository->isExpired($fd['ID']))
                continue;
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
                'eligibleFdList' => $eligibleFdList,
                'loanPlans' => $loanPlans
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* @var OnlineLoan $onlineLoanObj */
            $onlineLoanObj = $form->getData();
            $onlineLoanObj->setId(Uuid::v4());
            $onlineLoanObj->setUser($user);
            $onlineLoanObj->setStatus('APPROVED');
            $onlineLoanObj->setLoanMode('ONLINE');

            $loanPlanId = $onlineLoanObj->getPlanId();
            $loanPlan = $loanRepository->getLoanPlanById($loanPlanId);

            $loanRepository->insertOnlineLoan($onlineLoanObj);

            $instalmentSet = new InstalmentSet(
                $onlineLoanObj->getId(),
                $onlineLoanObj->getAmount(),
                $loanPlan['Interest_Rate'],
                $loanPlan['Duration']
            );
            $instalmentRepository->insertInstalmentSet($instalmentSet);

            return $this->redirectToRoute('app_loan_online');
        }

        return $this->renderForm('loan/online.html.twig', [
            'form' => $form,
            'controller_name' => 'LoanController',
            'loanEligibility' => $loanEligibility
        ]);
    }

    #[Route('/loan/request', name: 'app_loan_request')]
    public function request(
        Request $request,
        LoanRepository $loanRepository,
        UserRepository $userRepository
    ): Response
    {
        $loanData = [];
        $loanPlans = $loanRepository->getLoanPlans();

        $form = $this->createForm(
            LoanRequestType::class,
            $loanData,
            [
                'loanPlans' => $loanPlans
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $loanData = $form->getData();
            $normalLoan = new NormalLoan();

            $user = $userRepository->findOneByUsername($loanData['username']);

            $normalLoan->setId(Uuid::v4());
            $normalLoan->setUser($user);
            $normalLoan->setLoanType($loanData['loanType']);
            $normalLoan->setStatus('CREATED');
            $normalLoan->setAmount($loanData['amount']);
            $normalLoan->setLoanMode('NORMAL');
            $normalLoan->setPlanId($loanData['planId']);
            $normalLoan->setReason($loanData['reason']);
            $normalLoan->setAccountNumber($loanData['accountNumber']);

            $loanRepository->insertNormalLoan($normalLoan);
            return $this->redirectToRoute('app_loan_request');
        }

        return $this->renderForm('loan/request.html.twig', [
            'form' => $form,
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
