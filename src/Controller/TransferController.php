<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransferType;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    #[Route('/transfer', name: 'app_transfer', methods: ['GET', 'POST'])]
    public function index(Request $request, TransactionRepository $transactionRepository): Response
    {
        $user = $this->getUser();

        $transfer = new Transaction();
        $transfer->setType("TRANSFER");
        $form = $this->createForm(TransferType::class, $transfer, ['userId' => $user->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transfer = $form->getData();
            $transactionRepository->insert($transfer);
            return $this->redirectToRoute("app_account_view");
        }

        return $this->renderForm('transfer/index.html.twig', [
            'controller_name' => 'TransferController',
            'form' => $form,
        ]);
    }
}
