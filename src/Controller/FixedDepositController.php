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

    #[Route('/fixed-deposit', name: 'app_fixed_deposit')]
    public function index(
        Request           $request,
        AccountRepository $accountRepository,
        FdRepository      $fdRepository,
        MoneyUtils        $moneyUtils
    ): Response
    {
        $user = $this->getUser();

        $fd = [];
        $form = $this->createForm(FixedDepositType::class, $fd, ['userId' => $user->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fd = $form->getData();

            $savingsAccount = $accountRepository->findOne($fd['savingsAccount']);
            $amountInAccount = $moneyUtils->parseString($savingsAccount['Amount']);
            $amountToDeposit = $moneyUtils->parseString($fd['amount']);
            $newAmountInAccount = $amountInAccount->subtract($amountToDeposit);

            $conn = $this->em->getConnection();
            $conn->beginTransaction();
            try {
                $fdRepository->insert($fd);
                $accountRepository->updateAmount($savingsAccount['Account_Number'], $moneyUtils->format($newAmountInAccount));
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
