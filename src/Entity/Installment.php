<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Installment
 *
 * @ORM\Table(name="Installment", indexes={@ORM\Index(name="Loan_ID", columns={"Loan_ID"})})
 * @ORM\Entity
 */
class Installment
{
    /**
     * @var string
     *
     * @ORM\Column(name="ID", type="string", length=36, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="Year", type="integer", nullable=false)
     */
    private $year;

    /**
     * @var int
     *
     * @ORM\Column(name="Month", type="integer", nullable=false)
     */
    private $month;

    /**
     * @var string
     *
     * @ORM\Column(name="Amount", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="Status", type="string", length=0, nullable=false)
     */
    private $status;

    /**
     * @var \Loan
     *
     * @ORM\ManyToOne(targetEntity="Loan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Loan_ID", referencedColumnName="ID")
     * })
     */
    private $loan;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLoan(): ?Loan
    {
        return $this->loan;
    }

    public function setLoan(?Loan $loan): self
    {
        $this->loan = $loan;

        return $this;
    }


}
