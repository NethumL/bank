<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Normal Loan
 *
 * @ORM\Table(name="Normal_Loan", indexes={@ORM\Index(name="Normal_Loan", columns={"ID"})})
 * @ORM\Entity
 */
class NormalLoan extends Loan
{
    private $accountNumber;

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

}
