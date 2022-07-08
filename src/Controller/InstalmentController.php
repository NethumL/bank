<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\InstalmentPaymentType;
use App\Repository\AccountRepository;
use App\Repository\InstalmentRepository;
use App\Repository\TransactionRepository;
use App\Util\MoneyUtils;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstalmentController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/instalment/view', name: 'app_instalment_view', methods: ['GET'])]
    public function view(InstalmentRepository $instalmentRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $instalments = $instalmentRepository->findAllUnpaidByUser($user->getId());
        return $this->render('instalment/view.html.twig', [
            'instalments' => $instalments,
        ]);
    }

    #[Route('/instalment/pay/{id}', name: 'app_instalment_pay', methods: ['GET', 'POST'])]
    public function pay(
        string                $id,
        Request               $request,
        InstalmentRepository  $instalmentRepository,
        AccountRepository     $accountRepository,
        TransactionRepository $transactionRepository,
        MoneyUtils            $moneyUtils
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $instalment = $instalmentRepository->findOneById($id);
        if (!$instalment || $instalment['User_ID'] != $user->getId() || $instalment['Status'] == 'PAID') {
            return new RedirectResponse("/instalment/view");
        }

        $allAccounts = $accountRepository->findByUser($user->getId());
        $accounts = [];
        $amountToPay = $moneyUtils->parseString($instalment['Amount']);
        foreach ($allAccounts as $account) {
            $availableBalance = $moneyUtils->parseString($account['Amount']);
            if ($availableBalance->greaterThanOrEqual($amountToPay)) {
                $accounts[] = $account;
            }
        }

        $transaction = new Transaction();
        $transaction->setType("PAYMENT");
        $transaction->setAmount($instalment['Amount']);
        $form = $this->createForm(InstalmentPaymentType::class, $transaction, ['accounts' => $accounts]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conn = $this->em->getConnection();
            $conn->beginTransaction();

            try {
                $account = $accountRepository->findOne($transaction->getFrom());
                $newAmountInAccount = $moneyUtils->parseString($account['Amount'])->subtract($amountToPay);
                $accountRepository->updateAmount($account['Account_Number'], $moneyUtils->format($newAmountInAccount));
                $instalmentRepository->markAsPaid($id);
                $transactionRepository->insert($transaction);

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

            return $this->redirectToRoute("app_instalment_view");
        }

        return $this->renderForm('instalment/pay.html.twig', [
            'form' => $form,
            'instalment' => $instalment,
        ]);
    }
}
