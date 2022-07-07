<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AgeSatisfiesSavingsPlan extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Customer is {{ age }} but needs to be {{ age_range }} for this savings plan.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
