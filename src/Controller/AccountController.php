<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account/create', name: 'app_account_create')]
    public function create(): Response
    {
        return $this->render('account/create.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/account/create-new', name: 'app_account_create_new')]
    public function createNew(): Response
    {
        return $this->render('account/create_new.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/account/view', name: 'app_account_view')]
    public function view(): Response
    {
        return $this->render('account/view.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
}
