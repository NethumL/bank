<?php

namespace App\Repository;

use App\Entity\InstalmentSet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Uid\Uuid;

class InstalmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneById(string $instalmentId): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT I.*, L.User_ID FROM Installment I JOIN Loan L ON L.ID = I.Loan_ID WHERE I.ID = ?");
        $resultSet = $stmt->executeQuery([$instalmentId]);
        return $resultSet->fetchAssociative();
    }

    public function findAllUnpaidByUser(string $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("
            SELECT I.*, L.User_ID, L.Loan_Type
            FROM Installment I
            JOIN Loan L ON I.Loan_ID = L.ID
            WHERE User_ID = ? AND I.Status <> 'PAID'
            ORDER BY I.Year, I.Month
        ");
        $resultSet = $stmt->executeQuery([$userId]);
        return $resultSet->fetchAllAssociative();
    }

    public function markAsPaid(string $instalmentId): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE Installment SET Status='PAID' WHERE ID = ?");
        return $stmt->executeStatement([$instalmentId]);
    }

    public function insertInstalmentSet(InstalmentSet $instalmentSet)
    {
        $conn = $this->getEntityManager()->getConnection();
        $instalments = $instalmentSet->getInstalments();
        if (sizeof($instalments)) {
            $returnValue = 0;
            foreach($instalments as $instalment) {
                $stmtLoan = $conn->prepare("INSERT INTO Installment(ID, Loan_ID, Year, Month, Amount, Status) VALUES (?, ?, ?, ?, ?, ?)");
                $returnValue = $stmtLoan->executeStatement([
                    Uuid::v4(),
                    $instalmentSet->getLoanID(),
                    $instalment['year'],
                    $instalment['month'],
                    $instalment['amount'],
                    'CREATED'
                ]);
            }
            return $returnValue;
        }
        return false;
    }

    public function findLateInstalmentsByBranchIDAndDate(string $branchID, string $year, string $month)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("
            SELECT 
            `Installment`.`ID`,
            `Installment`.`Year`,
            `Installment`.`Month`,
            `Installment`.`Amount`,
            `Loan`.`ID` AS Loan_ID,
            `Loan`.`Loan_Type`,
            A.Account_Number AS Normal_Loan_Account,
            B.Account_Number AS Online_Loan_Account
            FROM `Installment`
            INNER JOIN `Loan` ON `Loan`.`ID`=`Installment`.`Loan_ID`
            LEFT JOIN `Normal_Loan` ON `Loan`.`ID`=`Normal_Loan`.`ID`
            LEFT JOIN `Online_Loan` ON `Loan`.`ID`=`Online_Loan`.`ID`
            LEFT JOIN `FD` ON `Online_Loan`.`FD_ID`=`FD`.`ID`
            LEFT JOIN `Account` AS A ON `Normal_Loan`.`Account_Number`=A.`Account_Number`
            LEFT JOIN `Account` AS B ON B.`Account_Number`=`FD`.`Account_Number`
            WHERE :branchID IN (A.Branch_ID, B.Branch_ID) AND
            (`Installment`.`Year`=:year AND `Installment`.`Month`<=:month) OR
            (`Installment`.`Year`<:year)
            ORDER BY Year, Month;
        ");
        $resultSet = $stmt->executeQuery([
            'branchID' => $branchID,
            'year' => $year,
            'month' => $month
        ]);
        return $resultSet->fetchAllAssociative();
    }
}
