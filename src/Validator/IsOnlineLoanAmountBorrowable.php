<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsOnlineLoanAmountBorrowable extends Constraint
{
    /*
         * Any public properties become valid options for the annotation.
         * Then, use these in your validator class.
         */
    public $message = 'Maximum borrowable loan amount for {{ fdId }} is {{ maxAmount }} LKR.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}