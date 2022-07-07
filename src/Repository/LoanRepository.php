<?php

namespace App\Repository;

use App\Entity\Loan;
use App\Entity\NormalLoan;
use App\Entity\OnlineLoan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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

    public function findNormalLoanByID(string $loanID): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Loan L INNER JOIN Normal_Loan USING(ID) WHERE L.ID = ?");
        $resultSet = $stmt->executeQuery([$loanID]);
        return $resultSet->fetchAssociative();
    }

    public function getAllNormalLoans(string $status=''): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $string = "SELECT L.*, Normal_Loan.Account_Number, User.Name, Loan_Plan.Duration, Loan_Plan.Interest_Rate FROM Loan L RIGHT JOIN Normal_Loan USING(ID) INNER JOIN User ON User.ID=L.User_ID INNER JOIN Loan_Plan ON Loan_Plan.ID=L.Plan_ID WHERE L.Loan_Mode='NORMAL'";
        if ($status !=='' && in_array($status, ['CREATED', 'APPROVED', 'PAID'], true)) {
            $string = $string . ' AND L.Status=?';
            $stmt = $conn->prepare($string);
            $resultSet = $stmt->executeQuery([$status]);
            return $resultSet->fetchAllAssociative();
        } else {
            $stmt = $conn->prepare($string);
            $resultSet = $stmt->executeQuery();
            return $resultSet->fetchAllAssociative();
        }
    }

    public function insertOnlineLoan(OnlineLoan $onlineLoan): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->beginTransaction();

        try {
            $stmtLoan = $conn->prepare("INSERT INTO Loan(ID, User_ID, Loan_Type, Status, Amount, Loan_Mode, Plan_ID) VALUES(?, ?, ?, ?, ?, ?, ?)");
            $stmtLoan->executeStatement([
                $onlineLoan->getId(),
                $onlineLoan->getUser()->getId(),
                $onlineLoan->getLoanType(),
                $onlineLoan->getStatus(),
                $onlineLoan->getAmount(),
                $onlineLoan->getLoanMode(),
                $onlineLoan->getPlanId()
            ]);
            $stmtOnlineLoan = $conn->prepare("INSERT INTO Online_Loan(ID, FD_ID) VALUES(?, ?)");
            $returnValue = $stmtOnlineLoan->executeStatement([
                $onlineLoan->getId(),
                $onlineLoan->getFdId()
            ]);
            $conn->commit();
            return $returnValue;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function insertNormalLoan(NormalLoan $loan)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->beginTransaction();

        try {
            $stmtLoan = $conn->prepare("INSERT INTO Loan(ID, User_ID, Loan_Type, Status, Amount, Loan_Mode, PLan_ID, Reason) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtLoan->executeStatement([
                $loan->getId(),
                $loan->getUser()->getId(),
                $loan->getLoanType(),
                $loan->getStatus(),
                $loan->getAmount(),
                $loan->getLoanMode(),
                $loan->getPlanId(),
                $loan->getReason()
            ]);
            $stmtNormalLoan = $conn->prepare("INSERT INTO Normal_Loan(ID, Account_Number) VALUES(?, ?)");
            $returnValue = $stmtNormalLoan->executeStatement([
                $loan->getId(),
                $loan->getAccountNumber()
            ]);
            $conn->commit();
            return $returnValue;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function getLoanPlans()
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Loan_Plan");
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getLoanPlanById(string $ID)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Loan_Plan WHERE ID=?");
        $resultSet = $stmt->executeQuery([$ID]);
        return $resultSet->fetchAssociative();
    }

    public function markAsApproved(string $ID)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE Loan SET Status='APPROVED' WHERE ID = ?");
        return $stmt->executeStatement([$ID]);
    }

    public function markAsRejected(string $ID)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE Loan SET Status='REJECTED' WHERE ID = ?");
        return $stmt->executeStatement([$ID]);
    }
}
