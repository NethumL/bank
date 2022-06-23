<?php

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    public function findAllByUser(string $userId): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Loan L WHERE L.User_ID = ?");
        $resultSet = $stmt->executeQuery([$userId]);
        return $resultSet->fetchAllAssociative();
    }

    public function findOnlineLoansByUser(string $userId): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Loan L RIGHT JOIN Online_Loan USING(ID) WHERE L.User_ID = ?");
        $resultSet = $stmt->executeQuery([$userId]);
        return $resultSet->fetchAllAssociative();
    }
}
