<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AccountRepository extends ServiceEntityRepository
{
    private static array $typeToCodeMap = [
        'SAVINGS' => '164',
        'CURRENT' => '925'
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function findByUser(string $userId, string $accountType = ""): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT A.*,Branch.Name AS Branch_Name FROM Account A JOIN Branch ON A.Branch_ID = Branch.ID WHERE A.User_ID = ?";
        $params = [$userId];
        if ($accountType) {
            $sql .= " AND A.Account_Type = ?";
            $params[] = $accountType;
        }
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($params);
        return $resultSet->fetchAllAssociative();
    }

    public function findOne(string $id): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Account WHERE Account_Number = ?");
        $result = $stmt->executeQuery([$id]);
        return $result->fetchAssociative();
    }

    public function updateAmount(string $id, string $newAmount): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE Account SET Amount = ? WHERE Account_Number = ?");
        return $stmt->executeStatement([$newAmount, $id]);
    }

    public function insert(array $account): string
    {
        $conn = $this->getEntityManager()->getConnection();

        $accountNumber = $this->getNextAccountNumber($account);

        $stmt = $conn->prepare("
            INSERT INTO Account(Account_Number, User_ID, Branch_ID, Account_Type, Amount)
            VALUES(?, ?, ?, ?, ?);
        ");
        $stmt->executeStatement([
            $accountNumber,
            $account['userId'],
            $account['branchId'],
            $account['accountType'],
            $account['amount'],
        ]);
        return $accountNumber;
    }

    public function getNextAccountNumber(array $accountDetails): string
    {
        $conn = $this->getEntityManager()->getConnection();

        $branch = $accountDetails['branchId'];
        $fromBranch = substr(base_convert(md5($branch), 16, 10), 0, 3);

        $fromType = self::$typeToCodeMap[$accountDetails['accountType']];

        $stmt = $conn->prepare("SELECT Account_Number FROM Account WHERE Branch_ID = ? ORDER BY Created_Time DESC LIMIT 1");
        $result = $stmt->executeQuery([$accountDetails['branchId']]);
        $prevAccountNumber = $result->fetchOne();
        if ($prevAccountNumber !== false) {
            $prevIncremental = substr($prevAccountNumber, 6, 7);
        } else {
            $prevIncremental = 0;
        }
        $fromIncremental = str_pad(strval(intval($prevIncremental) + 1), 7, '0', STR_PAD_LEFT);

        $fromRandom = strval(random_int(0, 999));

        return $fromBranch . $fromType . $fromIncremental . $fromRandom;
    }
}
