<?php

namespace App\Repository;

use App\Entity\ChoicePosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ChoicePosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChoicePosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChoicePosition[]    findAll()
 * @method ChoicePosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoicePositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChoicePosition::class);
    }

    // /**
    //  * @return ChoicePosition[] Returns an array of ChoicePosition objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function countAllChoice($idRank)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.rank = :val')
            ->setParameter('val',$idRank)
            ->getQuery()
            ->getSingleScalarResult();
        dump($qb);
        return $qb;
    }


    public function getMaxPosition($value)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('MAX(u.place)')
            ->andWhere('u.rank = :val')
            ->setParameter('val', $value);
        return $qb->getQuery()->getOneOrNullResult()[1];
    }

    public function findByRanking($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.rank = :val')
            ->andWhere('r.place IS NOT NULL')
            ->setParameter('val', $value)
            ->orderBy('r.place', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByRankingNoPlace($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.rank = :val')
            ->andWhere('r.place IS NULL')
            ->setParameter('val', $value)
            ->orderBy('r.place', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByRankingByPlace($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.rank = :val')
            ->andWhere('r.place IS NOT NULL')
            ->setParameter('val', $value)
            ->orderBy('r.place', 'ASC')
            ->getQuery()
            ->getResult();
    }



    /*
    public function findOneBySomeField($value): ?ChoicePosition
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
