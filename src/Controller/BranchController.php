<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Form\BranchType;
use App\Repository\BranchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BranchController extends AbstractController
{
    #[Route('/branch/new', name: 'app_branch_new')]
    public function new(Request $request, BranchRepository $branchRepository): Response
    {
        $branch = new Branch();
        $form = $this->createForm(BranchType::class, $branch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var Branch $branch */
            $branch = $form->getData();

            $branchRepository->insert($branch);
            return $this->redirectToRoute('app_branch_new');
        }

        return $this->renderForm('branch/new.html.twig', [
            'controller_name' => 'BranchController',
            'form' => $form
        ]);
    }
}
