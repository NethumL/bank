<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\DepositType;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use App\Util\MoneyUtils;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/deposit')]
class DepositController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_deposit')]
    public function index(
        Request               $request,
        TransactionRepository $transactionRepository,
        AccountRepository     $accountRepository,
        MoneyUtils            $moneyUtils
    ): Response
    {
        $deposit = new Transaction();
        $deposit->setType("DEPOSIT");
        $form = $this->createForm(DepositType::class, $deposit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $amountToDeposit = $moneyUtils->parseString($deposit->getAmount());

            $conn = $this->em->getConnection();
            $conn->beginTransaction();

            try {
                $account = $accountRepository->findOne($deposit->getTo());
                $newAmountInAccount = $moneyUtils->parseString($account['Amount'])->add($amountToDeposit);
                $accountRepository->updateAmount($account['Account_Number'], $moneyUtils->format($newAmountInAccount));
                $transactionRepository->insert($deposit);

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

            return $this->redirectToRoute("app_deposit");
        }

        return $this->renderForm('deposit/index.html.twig', [
            'form' => $form,
        ]);
    }
}
