<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SavingsPlan
 *
 * @ORM\Table(name="Savings_Plan")
 * @ORM\Entity
 */
class SavingsPlan
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="Interest_Rate", type="integer", nullable=false)
     */
    private $interestRate;

    /**
     * @var string
     *
     * @ORM\Column(name="Minimum_Balance", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $minimumBalance;

    /**
     * @var int
     *
     * @ORM\Column(name="Minimum_Age", type="integer", nullable=false)
     */
    private $minimumAge;

    /**
     * @var int
     *
     * @ORM\Column(name="Maximum_Age", type="integer", nullable=false)
     */
    private $maximumAge;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInterestRate(): ?int
    {
        return $this->interestRate;
    }

    public function setInterestRate(int $interestRate): self
    {
        $this->interestRate = $interestRate;

        return $this;
    }

    public function getMinimumBalance(): ?string
    {
        return $this->minimumBalance;
    }

    public function setMinimumBalance(string $minimumBalance): self
    {
        $this->minimumBalance = $minimumBalance;

        return $this;
    }

    public function getMinimumAge(): ?int
    {
        return $this->minimumAge;
    }

    public function setMinimumAge(int $minimumAge): self
    {
        $this->minimumAge = $minimumAge;

        return $this;
    }

    public function getMaximumAge(): ?int
    {
        return $this->maximumAge;
    }

    public function setMaximumAge(int $maximumAge): self
    {
        $this->maximumAge = $maximumAge;

        return $this;
    }


}
