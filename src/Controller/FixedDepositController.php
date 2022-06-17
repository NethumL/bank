<?php

namespace App\Controller;

use App\Form\FixedDepositType;
use App\Repository\AccountRepository;
use App\Repository\FdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FixedDepositController extends AbstractController
{
    #[Route('/fixed-deposit/{id}', name: 'app_fixed_deposit')]
    public function index(string $id, Request $request, AccountRepository $accountRepository, FdRepository $fdRepository): Response
    {
        $user = $this->getUser();

        $savingsAccounts = $accountRepository->findByUser($user->getId(), "SAVINGS");
        $savingsAccountNumbers = array_map(function ($account) {
            return $account['Account_Number'];
        }, $savingsAccounts);

        if (!in_array($id, $savingsAccountNumbers)) {
            return $this->redirectToRoute("app_account_view");
        }

        $fd = ['savingsAccount' => $id];
        $form = $this->createForm(FixedDepositType::class, $fd, ['userId' => $user->getId(), 'savingsAccount' => $id]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fd = $form->getData();
            $fd['savingsAccount'] = $id;
            $result = $fdRepository->insert($fd);
            if ($result === 1) {
                $savingsAccount = $accountRepository->findOne($id);
                // TODO: Replace floating point calculation
                $accountRepository->updateAmount($id, (float)$savingsAccount['Amount'] - $fd['amount']);
                return $this->redirectToRoute("app_account_view");
            }
        }

        return $this->renderForm('fixed_deposit/index.html.twig', [
            'controller_name' => 'FixedDepositController',
            'form' => $form,
        ]);
    }
}
