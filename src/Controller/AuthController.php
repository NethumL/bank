<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordChangeType;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/auth')]
class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_auth_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser())
            return new RedirectResponse("/");

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_auth_logout')]
    public function logout()
    {
        throw new Exception("This code is never executed");
    }

    #[Route('/change-password', name: 'app_auth_change_password')]
    public function changePassword(
        Request                     $request,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(PasswordChangeType::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordChange = $form->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $passwordChange['newPassword']);
            $userRepository->updatePassword($user->getId(), $hashedPassword);
            return $this->redirectToRoute("app_auth_logout");
        }

        return $this->renderForm('auth/change_password.html.twig', [
            'form' => $form,
        ]);
    }
}
