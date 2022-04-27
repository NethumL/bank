<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    #[Route('/transfer', name: 'app_transfer')]
    public function index(): Response
    {
        return $this->render('transfer/index.html.twig', [
            'controller_name' => 'TransferController',
        ]);
    }
}
