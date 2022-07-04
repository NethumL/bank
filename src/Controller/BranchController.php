<?php

namespace App\Controller;

use App\Form\BranchType;
use App\Repository\BranchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/branch')]
class BranchController extends AbstractController
{
    #[Route('/', name: 'app_branch_index', methods: ['GET'])]
    public function index(BranchRepository $branchRepository): Response
    {
        $branches = $branchRepository->findAll();

        return $this->render('branch/index.html.twig', [
            'branches' => $branches,
        ]);
    }

    #[Route('/new', name: 'app_branch_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BranchRepository $branchRepository): Response
    {
        $form = $this->createForm(BranchType::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $branch = $form->getData();
            $branchRepository->insert($branch);

            return $this->redirectToRoute('app_branch_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('branch/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_branch_show', methods: ['GET'])]
    public function show(string $id, BranchRepository $branchRepository): Response
    {
        $branch = $branchRepository->findOneById($id);

        return $this->render('branch/show.html.twig', [
            'branch' => $branch,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_branch_edit', methods: ['GET', 'POST'])]
    public function edit(string $id, Request $request, BranchRepository $branchRepository): Response
    {
        $branch = $branchRepository->findOneById($id);

        $form = $this->createForm(BranchType::class, $branch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $branch = $form->getData();
            $branchRepository->update($branch);

            return $this->redirectToRoute('app_branch_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('branch/edit.html.twig', [
            'branch' => $branch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_branch_delete', methods: ['POST'])]
    public function delete(string $id, Request $request, BranchRepository $branchRepository): Response
    {
        $branch = $branchRepository->findOneById($id);
        if ($this->isCsrfTokenValid('delete' . $branch['ID'], $request->request->get('_token'))) {
            $branchRepository->delete($id);
        }

        return $this->redirectToRoute('app_branch_index', [], Response::HTTP_SEE_OTHER);
    }
}
