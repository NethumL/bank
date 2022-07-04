<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Branch
 *
 * @ORM\Table(name="Branch")
 * @ORM\Entity
 */
class Branch
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
     * @ORM\Column(name="Address", type="string", length=100, nullable=false)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $managerId;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getManagerId(): ?string
    {
        return $this->managerId;
    }

    public function setManagerId(?string $managerId): self
    {
        $this->managerId = $managerId;

        return $this;
    }
}
