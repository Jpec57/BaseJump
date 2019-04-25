<?php

namespace App\Repository;

use App\Entity\ChangeUserRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ChangeUserRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChangeUserRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChangeUserRequest[]    findAll()
 * @method ChangeUserRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChangeUserRequestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ChangeUserRequest::class);
    }
}
