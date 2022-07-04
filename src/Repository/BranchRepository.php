<?php

namespace App\Repository;

use App\Entity\Branch;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 */
class BranchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Branch::class);
    }

    public function insert(array $branch): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("INSERT INTO Branch(ID, Name, Address) VALUES(?, ?, ?);");
        return $stmt->executeStatement([
            Uuid::v4(),
            $branch['Name'],
            $branch['Address'],
        ]);
    }

    public function findOneById(string $id): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("
            SELECT B.ID, B.Name, B.Address, B.Manager_ID, U.Name AS Manager_Name FROM Branch B
            LEFT JOIN User U ON B.Manager_ID = U.ID
            WHERE B.ID = ?
        ");
        $resultSet = $stmt->executeQuery([$id]);
        return $resultSet->fetchAssociative();
    }

    public function findAll(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("
            SELECT B.ID, B.Name, B.Address, B.Manager_ID, U.Name AS Manager_Name FROM Branch B
            LEFT JOIN User U ON B.Manager_ID = U.ID
        ");
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function updateManager(string $branchId, Uuid $managerId): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE Branch SET Manager_ID = ? WHERE ID = ?;");
        return $stmt->executeStatement([$managerId, $branchId]);
    }

    public function update(array $branch): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE Branch SET Name = ?, Address = ? WHERE ID = ?;");
        return $stmt->executeStatement([$branch['Name'], $branch['Address'], $branch['ID']]);
    }

    public function delete(string $id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("DELETE FROM Branch WHERE ID = ?;");
        return $stmt->executeStatement([$id]);
    }
}
