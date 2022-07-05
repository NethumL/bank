<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 */
class Employee extends User
{
    /**
     * @ORM\Column(name="ID", type="string", length=36, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="Branch_ID", type="string", length=36, nullable=false)
     */
    private $branchId;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getBranchId(): ?string
    {
        return $this->branchId;
    }

    public function setBranchId(string $branchId): self
    {
        $this->branchId = $branchId;

        return $this;
    }
}
