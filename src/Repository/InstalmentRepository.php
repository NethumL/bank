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

    public function findAllByUser(string $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT I.*, L.User_ID, L.Loan_Type FROM Installment I JOIN Loan L ON I.Loan_ID = L.ID WHERE User_ID = ?");
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
    }
}
