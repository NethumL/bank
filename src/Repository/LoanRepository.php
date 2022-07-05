<?php

namespace App\Repository;

use App\Entity\Loan;
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

    public function insertOnlineLoan(OnlineLoan $onlineLoan): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->beginTransaction();

        try {
            $stmtLoan = $conn->prepare("INSERT INTO Loan(ID, User_ID, Loan_Type, Status, Amount, Loan_Mode) VALUES(?, ?, ?, ?, ?, ?)");
            $stmtLoan->executeStatement([
                $onlineLoan->getId(),
                $onlineLoan->getUser()->getId(),
                $onlineLoan->getLoanType(),
                $onlineLoan->getStatus(),
                $onlineLoan->getAmount(),
                $onlineLoan->getLoanMode()
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

    public function getLoanPlans()
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Loan_Plan");
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
}
