<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 *
 * @ORM\Table(name="Transaction", indexes={@ORM\Index(name="From", columns={"From"})})
 * @ORM\Entity
 */
class Transaction
{
    /**
     * @var string
     *
     * @ORM\Column(name="Transaction_ID", type="string", length=36, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="To", type="string", length=20, nullable=false)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="Type", type="string", length=0, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="Amount", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var \Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="From", referencedColumnName="Account_Number")
     * })
     */
    private $from;

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getFrom(): ?Account
    {
        return $this->from;
    }

    public function setFrom(?Account $from): self
    {
        $this->from = $from;

        return $this;
    }


}