<?php

namespace App\Repository;

use App\Entity\Fd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class FdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fd::class);
    }

    public function insert(array $fd): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("INSERT INTO FD(ID, Account_Number, Plan_ID, Amount) VALUES(?, ?, ?, ?)");
        return $stmt->executeStatement([
            Uuid::v4(),
            $fd['savingsAccount'],
            $fd['plan'],
            $fd['amount']
        ]);
    }
}

