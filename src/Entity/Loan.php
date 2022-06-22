<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class Loan
{
    private $id;
    private $user;
    private $loanType;
    private $status;
    private $amount;
    private $loanMode;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLoanType(): ?string
    {
        return $this->loanType;
    }

    public function setLoanType(string $loanType): self
    {
        $this->loanType = $loanType;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getLoanMode(): ?string
    {
        return $this->loanMode;
    }

    public function setLoanMode(string $mode): self
    {
        $this->loanMode = $mode;

        return $this;
    }
}
