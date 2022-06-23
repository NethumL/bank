<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransferType;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use App\Util\MoneyUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    #[Route('/transfer', name: 'app_transfer', methods: ['GET', 'POST'])]
    public function index(
        Request               $request,
        AccountRepository     $accountRepository,
        TransactionRepository $transactionRepository,
        MoneyUtils            $moneyUtils
    ): Response
    {
        $user = $this->getUser();

        $transfer = new Transaction();
        $transfer->setType("TRANSFER");
        $form = $this->createForm(TransferType::class, $transfer, ['userId' => $user->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var Transaction */
            $transfer = $form->getData();
            $transfer->setType("TRANSFER");
            $transactionRepository->insert($transfer);

            $fromAccount = $accountRepository->findOne($transfer->getFrom());
            $amountToTransfer = $moneyUtils->parseString($transfer->getAmount());
            $toAccount = $accountRepository->findOne($transfer->getTo());

            $newAmountInFrom = $moneyUtils->parseString($fromAccount['Amount'])->subtract($amountToTransfer);
            $newAmountInTo = $moneyUtils->parseString($toAccount['Amount'])->add($amountToTransfer);

            $accountRepository->updateAmount($fromAccount['Account_Number'], $moneyUtils->format($newAmountInFrom));
            $accountRepository->updateAmount($toAccount['Account_Number'], $moneyUtils->format($newAmountInTo));

            return $this->redirectToRoute("app_account_view");
        }

        return $this->renderForm('transfer/index.html.twig', [
            'controller_name' => 'TransferController',
            'form' => $form,
        ]);
    }
}
