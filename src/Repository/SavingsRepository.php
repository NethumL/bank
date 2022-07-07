<?php

namespace App\Repository;

use App\Entity\Savings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SavingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Savings::class);
    }

    public function insert(string $accountNumber, string $planId): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("INSERT INTO Savings(Account_Number, Plan_ID) VALUES(?, ?);");
        return $stmt->executeStatement([
            $accountNumber,
            $planId,
        ]);
    }
}
