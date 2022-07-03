<?php

namespace App\Controller;

use App\Entity\Employee;
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
        if ($this->isGranted('ROLE_ADMIN')) {
            $userTypeChoices = ['MANAGER', 'EMPLOYEE'];
        } else {
            $userTypeChoices = ['EMPLOYEE'];
        }

        $newEmployee = new Employee();
        $form = $this->createForm(NewEmployeeType::class, $newEmployee, ['userTypeChoices' => $userTypeChoices]);
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
