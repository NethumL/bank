<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/auth/login', name: 'app_auth_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser())
            return new RedirectResponse("/");

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'controller_name' => 'AuthController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/auth/logout', name: 'app_auth_logout')]
    public function logout()
    {
        throw new Exception("This code is never executed");
    }
}
