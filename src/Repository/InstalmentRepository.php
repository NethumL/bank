<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InstalmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllByUser(string $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT I.*, L.User_ID, L.Loan_Type FROM Installment I JOIN Loan L ON I.Loan_ID = L.ID WHERE User_ID = ?");
        $resultSet = $stmt->executeQuery([$userId]);
        return $resultSet->fetchAllAssociative();
    }
}