<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Savings
 *
 * @ORM\Table(name="Savings", indexes={@ORM\Index(name="Plan_ID", columns={"Plan_ID"})})
 * @ORM\Entity
 */
class Savings
{
    /**
     * @var \Account
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Account")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Account_Number", referencedColumnName="Account_Number")
     * })
     */
    private $accountNumber;

    /**
     * @var \SavingsPlan
     *
     * @ORM\ManyToOne(targetEntity="SavingsPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Plan_ID", referencedColumnName="ID")
     * })
     */
    private $plan;

    public function getAccountNumber(): ?Account
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?Account $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getPlan(): ?SavingsPlan
    {
        return $this->plan;
    }

    public function setPlan(?SavingsPlan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }


}
