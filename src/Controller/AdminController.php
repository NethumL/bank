<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/new', name: 'app_admin_new')]
    public function new(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository              $userRepository
    ): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $userTypeChoices = ['MANAGER', 'EMPLOYEE'];
        } else {
            $userTypeChoices = ['EMPLOYEE'];
        }

        $newUser = new User();
        $form = $this->createForm(NewUserType::class, $newUser, ['userTypeChoices' => $userTypeChoices]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $newUser */
            $newUser = $form->getData();
            $hashedPassword = $passwordHasher->hashPassword($newUser, $newUser->getPassword());
            $newUser->setPassword($hashedPassword);
            $userRepository->insert($newUser);
        }

        return $this->renderForm('admin/new.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form
        ]);
    }
}
