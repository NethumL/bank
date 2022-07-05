<?php

namespace App\Validator;

use App\Repository\AccountRepository;
use App\Repository\UserRepository;
use App\Util\MoneyUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AreUsernameAndAccountNumberValidValidator extends ConstraintValidator
{
    private UserRepository $userRepository;
    private AccountRepository $accountRepository;

    public function __construct(UserRepository $userRepository, AccountRepository $accountRepository)
    {
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param array $value
     * @param AreUsernameAndAccountNumberValid $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (
            $value['username'] === null || $value['username'] === ''
            || $value['accountNumber'] === null || $value['accountNumber'] === ''
        ) {
            return;
        }

        $user = $this->userRepository->findOneByUsername($value['username']);
        if ($user) {
            $savingsAccountNumbers = array_map(
                function ($account) {
                    return $account['Account_Number'];
                },
                $this->accountRepository->findByUser($user->getId(), 'SAVINGS')
            );

            if (!in_array($value['accountNumber'], $savingsAccountNumbers, true)) {
                $this->context->buildViolation('Invalid savings account number')
                    ->atPath('accountNumber')
                    ->addViolation();
            }
        } else {
            $this->context->buildViolation('Username is invalid.')
                ->atPath('username')
                ->addViolation();
        }
    }
}
