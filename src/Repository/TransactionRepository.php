<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneById(string $transactionId): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Transaction T WHERE T.Transaction_ID = ?");
        $resultSet = $stmt->executeQuery([$transactionId]);
        return $resultSet->fetchAssociative();
    }

    public function insert(Transaction $transaction): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("INSERT INTO Transaction VALUES(?, ?, ?, ?, ?)");
        return $stmt->executeStatement([
            Uuid::v4(),
            $transaction->getFrom(),
            $transaction->getTo(),
            $transaction->getType(),
            $transaction->getAmount(),
        ]);
    }
}
