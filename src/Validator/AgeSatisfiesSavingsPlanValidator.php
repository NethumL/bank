<?php

namespace App\Validator;

use App\Repository\SavingsPlanRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AgeSatisfiesSavingsPlanValidator extends ConstraintValidator
{
    private SavingsPlanRepository $savingsPlanRepository;
    private UserRepository $userRepository;

    public function __construct(SavingsPlanRepository $savingsPlanRepository, UserRepository $userRepository)
    {
        $this->savingsPlanRepository = $savingsPlanRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $value
     * @param AgeSatisfiesSavingsPlan $constraint
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
        $user = $this->userRepository->findOneByUsername($value['username']);

        $dob = $user->getDob();
        $age = $dob->diff(new DateTime())->y;

        $minimumAge = $savingsPlan['Minimum_Age'] ?? 0;
        $maximumAge = $savingsPlan['Maximum_Age'] ?? '';

        if ($age < $minimumAge || (!empty($maximumAge) && $age > $maximumAge)) {
            $ageRange = strval($minimumAge);
            if (empty($maximumAge)) {
                $ageRange .= "+";
            } else {
                $ageRange .= "-" . $maximumAge;
            }

            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ age_range }}', $ageRange)
                ->setParameter('{{ age }}', $age)
                ->atPath('savingsPlan')
                ->addViolation();
        }
    }
}
