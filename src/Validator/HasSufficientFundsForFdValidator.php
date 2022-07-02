<?php

namespace App\Validator;

use App\Repository\AccountRepository;
use App\Util\MoneyUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasSufficientFundsForFdValidator extends ConstraintValidator
{
    private AccountRepository $accountRepository;
    private MoneyUtils $moneyUtils;

    public function __construct(AccountRepository $accountRepository, MoneyUtils $moneyUtils)
    {
        $this->accountRepository = $accountRepository;
        $this->moneyUtils = $moneyUtils;
    }

    /**
     * @param array $value
     * @param HasSufficientFundsForFd $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value
            || null === $value['savingsAccount'] || '' === $value['savingsAccount']
            || null === $value['plan'] || '' === $value['plan']
            || null === $value['amount'] || '' === $value['amount']
            || null === $value['agree'] || '' === $value['agree']
        ) {
            return;
        }

        $savingsAccount = $this->accountRepository->findOne($value['savingsAccount']);
        $availableAmount = $this->moneyUtils->parseString($savingsAccount['Amount']);
        $amountToTransfer = $this->moneyUtils->parseString($value['amount']);

        if ($availableAmount->lessThan($amountToTransfer)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ savingsAccount }}', $value['savingsAccount'])
                ->atPath('savingsAccount')
                ->addViolation();
        }
    }
}
