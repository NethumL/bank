<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\BranchType;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/account/edit', name: 'app_account_edit')]
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $userRepository->update($user);

            return $this->redirectToRoute('app_account_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
