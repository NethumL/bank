<?php

namespace App\Repository;

use App\Entity\FdPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FdPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FdPlan::class);
    }

    public function findAll(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM FD_Plan");
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
}

