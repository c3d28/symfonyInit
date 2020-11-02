<?php

namespace App\Repository;

use App\Entity\TeamFifa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TeamFifa|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamFifa|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamFifa[]    findAll()
 * @method TeamFifa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamFifaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamFifa::class);
    }

    // /**
    //  * @return TeamFifa[] Returns an array of TeamFifa objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TeamFifa
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
