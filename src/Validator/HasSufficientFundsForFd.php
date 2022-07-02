<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HasSufficientFundsForFd extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Account {{ savingsAccount }} has insufficient funds.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
