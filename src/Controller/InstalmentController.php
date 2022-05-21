<?php

namespace App\Controller;

use App\Repository\InstalmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/instalment/pay', name: 'app_instalment_pay')]
    public function pay(): Response
    {
        return $this->render('instalment/pay.html.twig', [
            'controller_name' => 'InstalmentController',
        ]);
    }
}
