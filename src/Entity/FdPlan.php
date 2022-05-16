<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FdPlan
 *
 * @ORM\Table(name="FD_Plan")
 * @ORM\Entity
 */
class FdPlan
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
     * @ORM\Column(name="Duration", type="integer", nullable=false)
     */
    private $duration;

    /**
     * @var int
     *
     * @ORM\Column(name="Interest_Rate", type="integer", nullable=false)
     */
    private $interestRate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
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


}
