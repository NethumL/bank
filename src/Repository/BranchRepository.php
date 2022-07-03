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

    public function insert(Branch $branch): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("INSERT INTO Branch(ID, Name, Address) VALUES(?, ?, ?);");
        return $stmt->executeStatement([
            Uuid::v4(),
            $branch->getName(),
            $branch->getAddress(),
        ]);
    }

    public function findAll(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Branch");
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function updateManager(string $branchId, Uuid $managerId): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE Branch SET Manager_ID = ? WHERE ID = ?;");
        return $stmt->executeStatement([$managerId, $branchId]);
    }
}
