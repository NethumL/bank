<?php

namespace App\Validator;

use App\Repository\SavingsPlanRepository;
use App\Util\MoneyUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BalanceSatisfiesSavingsPlanValidator extends ConstraintValidator
{
    private SavingsPlanRepository $savingsPlanRepository;
    private MoneyUtils $moneyUtils;

    public function __construct(SavingsPlanRepository $savingsPlanRepository, MoneyUtils $moneyUtils)
    {
        $this->savingsPlanRepository = $savingsPlanRepository;
        $this->moneyUtils = $moneyUtils;
    }

    /**
     * @param array $value
     * @param BalanceSatisfiesSavingsPlan $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (empty($value)
            || empty($value['username'])
            || empty($value['accountType'])
            || empty($value['savingsPlan'])
            || empty($value['amount'])
        ) {
            return;
        }

        if ($value['accountType'] !== 'SAVINGS') {
            return;
        }

        $savingsPlan = $this->savingsPlanRepository->findOneById($value['savingsPlan']);
        $minimumBalance = $this->moneyUtils->parseString($savingsPlan['Minimum_Balance']);
        $balance = $this->moneyUtils->parseString($value['amount']);

        if ($balance->lessThan($minimumBalance)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ minBalance }}', $savingsPlan['Minimum_Balance'])
                ->atPath('amount')
                ->addViolation();
        }
    }
}
