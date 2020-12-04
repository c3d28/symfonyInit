<?php

namespace App\Repository;

use App\Entity\Christmas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Christmas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Christmas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Christmas[]    findAll()
 * @method Christmas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChristmasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Christmas::class);
    }

}
