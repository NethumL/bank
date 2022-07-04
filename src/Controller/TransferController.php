<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\TransferType;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use App\Util\MoneyUtils;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/transfer', name: 'app_transfer', methods: ['GET', 'POST'])]
    public function index(
        Request               $request,
        AccountRepository     $accountRepository,
        TransactionRepository $transactionRepository,
        MoneyUtils            $moneyUtils
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $transfer = new Transaction();
        $transfer->setType("TRANSFER");
        $form = $this->createForm(TransferType::class, $transfer, ['userId' => $user->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Transaction */
            $transfer = $form->getData();
            $transfer->setType("TRANSFER");

            $fromAccount = $accountRepository->findOne($transfer->getFrom());
            $amountToTransfer = $moneyUtils->parseString($transfer->getAmount());
            $toAccount = $accountRepository->findOne($transfer->getTo());

            $newAmountInFrom = $moneyUtils->parseString($fromAccount['Amount'])->subtract($amountToTransfer);
            $newAmountInTo = $moneyUtils->parseString($toAccount['Amount'])->add($amountToTransfer);

            $conn = $this->em->getConnection();
            $conn->beginTransaction();

            try {
                $transactionRepository->insert($transfer);
                $accountRepository->updateAmount($fromAccount['Account_Number'], $moneyUtils->format($newAmountInFrom));
                $accountRepository->updateAmount($toAccount['Account_Number'], $moneyUtils->format($newAmountInTo));
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

            return $this->redirectToRoute("app_account_view");
        }

        return $this->renderForm('transfer/index.html.twig', [
            'controller_name' => 'TransferController',
            'form' => $form,
        ]);
    }

    #[Route('/transfer/history', name: 'app_transfer_history', methods: ['GET'])]
    public function history(TransactionRepository $transactionRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $transfers = $transactionRepository->findByUser($user->getId());
        return $this->render('transfer/history.html.twig', [
            'controller_name' => 'TransferController',
            'transfers' => $transfers
        ]);
    }
}
