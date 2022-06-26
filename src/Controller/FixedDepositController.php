<?php

namespace App\Controller;

use App\Form\FixedDepositType;
use App\Repository\AccountRepository;
use App\Repository\FdRepository;
use App\Util\MoneyUtils;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FixedDepositController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/fixed-deposit/{id}', name: 'app_fixed_deposit', defaults: ['id' => ''])]
    public function index(
        string            $id,
        Request           $request,
        AccountRepository $accountRepository,
        FdRepository      $fdRepository,
        MoneyUtils        $moneyUtils
    ): Response
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

            $savingsAccount = $accountRepository->findOne($id);
            $amountInAccount = $moneyUtils->parseString($savingsAccount['Amount']);
            $amountToDeposit = $moneyUtils->parseString($fd['amount']);
            $newAmountInAccount = $amountInAccount->subtract($amountToDeposit);

            $conn = $this->em->getConnection();
            $conn->beginTransaction();
            try {
                $fdRepository->insert($fd);
                $accountRepository->updateAmount($id, $moneyUtils->format($newAmountInAccount));
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }
            return $this->redirectToRoute("app_account_view");
        }

        return $this->renderForm('fixed_deposit/index.html.twig', [
            'controller_name' => 'FixedDepositController',
            'form' => $form,
        ]);
    }
}
