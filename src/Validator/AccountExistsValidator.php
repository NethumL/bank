<?php

namespace App\Validator;

use App\Repository\AccountRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AccountExistsValidator extends ConstraintValidator
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param string $value
     * @param AccountExists $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->accountRepository->findOne($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ account }}', $value)
                ->addViolation();
        }
    }
}
