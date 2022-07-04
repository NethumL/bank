<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function insert(User $user): Uuid
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("
            INSERT INTO User(ID, Username, Name, Password, User_Type, Phone_Number, DOB, Address)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?);
        ");
        $uuid = Uuid::v4();
        $stmt->executeStatement([
            $uuid,
            $user->getUsername(),
            $user->getName(),
            $user->getPassword(),
            $user->getUserType(),
            $user->getPhoneNumber(),
            $user->getDob()->format('Y-m-d'),
            $user->getAddress()
        ]);
        return $uuid;
    }

    public function findOneByUsername(string $username): array|bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM User WHERE User.username = ?';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([$username]);

        return $resultSet->fetchAssociative();
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM User WHERE User.Username = ?;");
        $result = $stmt->executeQuery([$identifier]);
        return $result->fetchAssociative();
    }
}
