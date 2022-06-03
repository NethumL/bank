<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function findByUser(string $userId): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Account A WHERE A.User_ID = ?");
        $resultSet = $stmt->executeQuery([$userId]);
        return $resultSet->fetchAllAssociative();
    }
}
