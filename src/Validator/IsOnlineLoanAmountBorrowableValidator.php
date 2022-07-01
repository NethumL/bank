<?php

namespace App\Validator;

use App\Entity\OnlineLoan;
use App\Repository\FdRepository;
use App\Util\MoneyUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsOnlineLoanAmountBorrowableValidator extends ConstraintValidator
{
    private FdRepository $fdRepository;
    private MoneyUtils $moneyUtils;

    public function __construct(FdRepository $fdRepository, MoneyUtils $moneyUtils)
    {
        $this->fdRepository = $fdRepository;
        $this->moneyUtils = $moneyUtils;
    }

    /**
     * @param OnlineLoan $value
     * @param IsOnlineLoanAmountBorrowable $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if
        (
            null === $value ||
            '' === $value ||
            null === $value->getFdId() ||
            null === $value->getAmount()
        ) {
            return;
        }

        $fdId = $value->getFdId();
        $fd = $this->fdRepository->findOne($fdId);
        $fdAmount = $this->moneyUtils->parseString($fd['Amount']);
        $loanAmount = $this->moneyUtils->parseString($value->getAmount());

        $onlineLoanUpperBound = $this->moneyUtils->parseString('500000.00');
        $fdFraction = $fdAmount->multiply('0.6');
        $allowableAmount = $onlineLoanUpperBound->lessThan($fdFraction) ? $onlineLoanUpperBound : $fdFraction;

        if ($loanAmount->greaterThan($allowableAmount)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ fdId }}', $fdId)
                ->setParameter(('{{ maxAmount }}'), $this->moneyUtils->format(($allowableAmount)))
                ->atPath('amount')
                ->addViolation();
        }


    }
}
