<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
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

    public function findOneByUsername(string $username): User|bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM User WHERE User.username = ?';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([$username]);
        $userArray = $resultSet->fetchAssociative();

        if ($userArray) {
            $user = new User();
            $user->setId($userArray['ID']);
            $user->setUsername($userArray['Username']);
            $user->setPassword($userArray['Password']);
            $user->setName($userArray['Name']);
            $user->setUserType($userArray['User_Type']);
            $user->setPhoneNumber($userArray['Phone_Number']);
            $user->setDob(DateTime::createFromFormat('Y-m-d', $userArray['DOB']));
            $user->setAddress($userArray['Address']);
            return $user;
        }
        return $userArray;
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM User WHERE User.Username = ?;");
        $result = $stmt->executeQuery([$identifier]);
        return $result->fetchAssociative();
    }

    public function update(User $user): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE User SET Name = ?, Phone_Number = ?, DOB = ?, Address = ? WHERE ID = ?;");
        return $stmt->executeStatement([
            $user->getName(),
            $user->getPhoneNumber(),
            $user->getDob()->format('Y-m-d'),
            $user->getAddress(),
            $user->getId(),
        ]);
    }

    public function updatePassword(string $id, string $newPassword): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("UPDATE User SET Password = ? WHERE ID = ?;");
        return $stmt->executeStatement([$newPassword, $id]);
    }
}
