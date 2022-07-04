<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\User;
use App\Form\NewEmployeeType;
use App\Repository\BranchRepository;
use App\Repository\EmployeeRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/admin/new', name: 'app_admin_new')]
    public function new(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository              $userRepository,
        EmployeeRepository          $employeeRepository,
        BranchRepository            $branchRepository,
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $userTypes = ['MANAGER', 'EMPLOYEE'];
            $branches = $branchRepository->findAll();
        } else {
            $userTypes = ['EMPLOYEE'];
            $loggedInEmployee = $employeeRepository->findOneById($user->getId());
            $branches = [$branchRepository->findOneById($loggedInEmployee['Branch_ID'])];
        }

        $newEmployee = new Employee();
        $form = $this->createForm(NewEmployeeType::class, $newEmployee, [
            'userTypes' => $userTypes,
            'branches' => $branches,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Employee $newEmployee */
            $newEmployee = $form->getData();
            $hashedPassword = $passwordHasher->hashPassword($newEmployee, $newEmployee->getPassword());
            $newEmployee->setPassword($hashedPassword);

            $conn = $this->em->getConnection();
            $conn->beginTransaction();

            try {
                $id = $userRepository->insert($newEmployee);
                $employeeRepository->insert($newEmployee, $id);
                if ($newEmployee->getUserType() === 'MANAGER') {
                    $branchRepository->updateManager($newEmployee->getBranchId(), $id);
                }
                $conn->commit();
                return $this->redirectToRoute('app_admin_new');
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }
        }

        return $this->renderForm('admin/new.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form
        ]);
    }
}
