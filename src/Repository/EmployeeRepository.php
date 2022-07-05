<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function findOneById(string $id): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM Employee E JOIN User U ON E.ID = U.ID WHERE E.ID = ?';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([$id]);
        return $resultSet->fetchAssociative();
    }

    public function insert(Employee $employee, Uuid $id): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("INSERT INTO Employee(ID, Branch_ID) VALUES(?, ?);");
        return $stmt->executeStatement([
            $id,
            $employee->getBranchId(),
        ]);
    }
}
