<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\IsOnlineLoanAmountBorrowable;

/**
 * Online Loan
 *
 * @ORM\Table(name="Online_Loan", indexes={@ORM\Index(name="Online_Loan", columns={"ID"})})
 * @ORM\Entity
 * @IsOnlineLoanAmountBorrowable
 */
class OnlineLoan extends Loan
{
    private $fdId;

    public function getFdId(): ?string
    {
        return $this->fdId;
    }

    public function setFdId(string $fdId): self
    {
        $this->fdId = $fdId;

        return $this;
    }

}
