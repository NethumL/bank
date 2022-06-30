<?php

namespace App\Validator;

use App\Entity\Transaction;
use App\Repository\AccountRepository;
use App\Util\MoneyUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasSufficientFundsValidator extends ConstraintValidator
{
    private AccountRepository $accountRepository;
    private MoneyUtils $moneyUtils;

    public function __construct(AccountRepository $accountRepository, MoneyUtils $moneyUtils)
    {
        $this->accountRepository = $accountRepository;
        $this->moneyUtils = $moneyUtils;
    }

    /**
     * @param Transaction $value
     * @param HasSufficientFunds $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value
            || null === $value->getFrom() || '' === $value->getFrom()
            || null === $value->getTo() || '' === $value->getTo()
            || null === $value->getAmount() || '' === $value->getAmount()
        ) {
            return;
        }

        $fromAccount = $this->accountRepository->findOne($value->getFrom());
        $availableAmount = $this->moneyUtils->parseString($fromAccount['Amount']);
        $amountToTransfer = $this->moneyUtils->parseString($value->getAmount());

        if ($availableAmount->lessThan($amountToTransfer)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ from }}', $value->getFrom())
                ->atPath('from')
                ->addViolation();
        }
    }
}
