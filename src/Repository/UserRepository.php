<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUsername(string $username): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM User WHERE User.username = ?';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([$username]);

        return $resultSet->fetchAssociative();
    }
}
