<?php

namespace App\Controller;

use App\Form\InstalmentPaymentType;
use App\Repository\InstalmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstalmentController extends AbstractController
{
    #[Route('/instalment/view', name: 'app_instalment_view', methods: ['GET'])]
    public function view(InstalmentRepository $instalmentRepository): Response
    {
        $user = $this->getUser();
        $instalments = $instalmentRepository->findAllByUser($user->getId());
        return $this->render('instalment/view.html.twig', [
            'instalments' => $instalments,
        ]);
    }

    #[Route('/instalment/pay/{id}', name: 'app_instalment_pay', methods: ['GET', 'POST'])]
    public function pay(string $id, Request $request, InstalmentRepository $instalmentRepository): Response
    {
        $user = $this->getUser();
        $instalment = $instalmentRepository->findOneById($id);
        if (!$instalment || $instalment['User_ID'] != $user->getId() || $instalment['Status'] == 'PAID') {
            return new RedirectResponse("/instalment/view");
        }

        $instalmentPayment = [];
        $form = $this->createForm(InstalmentPaymentType::class, $instalmentPayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instalmentPayment = $form->getData();
            $result = $instalmentRepository->markAsPaid($id);
            return $this->redirectToRoute("app_instalment_view");
        }

        return $this->renderForm('instalment/pay.html.twig', [
            'form' => $form,
            'instalment' => $instalment,
        ]);
    }
}
