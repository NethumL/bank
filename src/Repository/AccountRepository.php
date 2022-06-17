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

    public function findByUser(string $userId, string $accountType = ""): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM Account A WHERE A.User_ID = ?";
        $params = [$userId];
        if ($accountType) {
            $sql .= " AND A.Account_Type = ?";
            $params[] = $accountType;
        }
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($params);
        return $resultSet->fetchAllAssociative();
    }
}
