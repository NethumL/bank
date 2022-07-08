<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\CustomerType;
use App\Repository\AccountRepository;
use App\Repository\EmployeeRepository;
use App\Repository\FdRepository;
use App\Repository\LoanRepository;
use App\Repository\SavingsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/account/create', name: 'app_account_create')]
    public function create(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository              $userRepository,
    ): Response

    {
        $customer = new User();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $customer */
            $customer = $form->getData();
            $customer->setUserType('CUSTOMER');
            $hashedPassword = $passwordHasher->hashPassword($customer, $customer->getPassword());
            $customer->setPassword($hashedPassword);
            $userRepository->insert($customer);
            return $this->redirectToRoute('app_account_create');
        }

        return $this->renderForm('account/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/account/create-new', name: 'app_account_create_new')]
    public function createNew(
        Request            $request,
        UserRepository     $userRepository,
        EmployeeRepository $employeeRepository,
        AccountRepository  $accountRepository,
        SavingsRepository  $savingsRepository,
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $employee = $employeeRepository->findOneById($user->getId());

        $form = $this->createForm(AccountType::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array $account */
            $account = $form->getData();
            $account['branchId'] = $employee['Branch_ID'];

            $customer = $userRepository->findOneByUsername($account['username']);
            $account['userId'] = $customer->getId();

            $conn = $this->em->getConnection();
            $conn->beginTransaction();

            try {
                $accountNumber = $accountRepository->insert($account);
                if ($account['accountType'] === 'SAVINGS') {
                    $savingsRepository->insert($accountNumber, $account['savingsPlan']);
                }

                $this->addFlash("new-account-number", "New account number is " . $accountNumber . ".");

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

            return $this->redirectToRoute("app_account_create_new");
        }

        return $this->renderForm('account/create_new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/account/view', name: 'app_account_view')]
    public function view(AccountRepository $accountRepository, FdRepository $fdRepository, LoanRepository $loanRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $accounts = $accountRepository->findByUser($user->getId());
        $fds = $fdRepository->findByUser($user->getId());
        $loans = $loanRepository->findAllUnpaidByUser($user->getId());

        return $this->render('account/view.html.twig', [
            'accounts' => $accounts,
            'fds' => $fds,
            'loans' => $loans,
        ]);
    }
}