<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fd
 *
 * @ORM\Table(name="FD", indexes={@ORM\Index(name="Plan_ID", columns={"Plan_ID"})})
 * @ORM\Entity
 */
class Fd
{
    /**
     * @var string
     *
     * @ORM\Column(name="ID", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Account_Number", type="string", length=20, nullable=false)
     */
    private $accountNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Created_Time", type="datetime", nullable=false)
     */
    private $createdTime;

    /**
     * @var \FdPlan
     *
     * @ORM\ManyToOne(targetEntity="FdPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Plan_ID", referencedColumnName="ID")
     * })
     */
    private $plan;

    /**
     * @var string
     *
     * @ORM\Column(name="Amount", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $amount;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getCreatedTime(): ?\DateTimeInterface
    {
        return $this->createdTime;
    }

    public function setCreatedTime(\DateTimeInterface $createdTime): self
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    public function getPlan(): ?FdPlan
    {
        return $this->plan;
    }

    public function setPlan(?FdPlan $plan): self
    {
        $this->plan = $plan;

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
}
