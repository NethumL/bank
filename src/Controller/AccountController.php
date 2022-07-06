<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\AccountRepository;
use App\Repository\FdRepository;
use App\Form\CustomerType;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    #[Route('/account/create', name: 'app_account_create')]
    public function create(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository              $userRepository,
    ): Response

    {
        /** @var User $user */
        $user = $this->getUser();
        $newUser = new User();
        $form = $this->createForm(CustomerType::class, $newUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $newUser */
            $newUser = $form->getData();
            $newUser->setUserType('CUSTOMER');
            $hashedPassword = $passwordHasher->hashPassword($newUser, $newUser->getPassword());
            $newUser->setPassword($hashedPassword);
            $userRepository->insert($newUser);
            return $this->redirectToRoute('app_account_create');
        }

        return $this->renderForm('account/create.html.twig', [
            'controller_name' => 'AccountController',
            'form' => $form
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
    public function view(AccountRepository $accountRepository, FdRepository $fdRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $accounts = $accountRepository->findByUser($user->getId());
        $fds = $fdRepository->findByUser($user->getId());

        return $this->render('account/view.html.twig', [
            'controller_name' => 'AccountController',
            'accounts' => $accounts,
            'fds' => $fds
        ]);
    }
}
