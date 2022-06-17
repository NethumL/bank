<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Loan
 *
 * @ORM\Table(name="Loan", indexes={@ORM\Index(name="User_ID", columns={"User_ID"})})
 * @ORM\Entity
 */
class Loan
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
     * @var string
     *
     * @ORM\Column(name="Loan_Type", type="string", length=0, nullable=false)
     */
    private $loanType;

    /**
     * @var string
     *
     * @ORM\Column(name="Status", type="string", length=0, nullable=false)
     */
    private $status;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="User_ID", referencedColumnName="ID")
     * })
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Created_Time", type="datetime", nullable=false)
     */
    private $createdTime;

    public function getId(): ?string
    {
        return $this->id;
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


}
