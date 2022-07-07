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
        $stmt = $conn->prepare("INSERT INTO Transaction(Transaction_ID, `From`, `To`, Type, Amount, Description) VALUES(?, ?, ?, ?, ?, ?)");
        return $stmt->executeStatement([
            Uuid::v4(),
            $transaction->getFrom(),
            $transaction->getTo(),
            $transaction->getType(),
            $transaction->getAmount(),
            $transaction->getDescription(),
        ]);
    }

    public function findByUser(string $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare( "
            SELECT * FROM Transaction T LEFT JOIN Account A on T.`From` = A.Account_Number
            WHERE A.User_ID = ?
        ");
        return $stmt->executeQuery([$userId])->fetchAllAssociative();
    }

    public function findByBranchIDAndDate(string $branchID, string $date): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare( "
            SELECT `Transaction`.*, `P`.`Name` AS From_Branch_Name, `Q`.`Name` AS To_Branch_Name FROM `Transaction`
            LEFT JOIN `Account` AS A ON `A`.`Account_Number`=`Transaction`.`From`
            LEFT JOIN `Account` AS B ON `B`.`Account_Number`=`Transaction`.`To`
            LEFT JOIN `Branch` AS P ON `P`.`ID`=`A`.`Branch_ID`
            LEFT JOIN `Branch` AS Q ON `Q`.`ID`=`B`.`Branch_ID`
            WHERE ? IN (`A`.`Branch_ID`, `B`.`Branch_ID`)
            AND
            DATE(`Transaction`.`Created_Time`)=?
        ");
        return $stmt->executeQuery([$branchID, $date])->fetchAllAssociative();
    }
}
