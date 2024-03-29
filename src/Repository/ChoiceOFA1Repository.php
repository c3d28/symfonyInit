<?php

namespace App\Repository;

use App\Entity\ChoiceOFA1;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChoiceOFA1|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChoiceOFA1|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChoiceOFA1[]    findAll()
 * @method ChoiceOFA1[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoiceOFA1Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChoiceOFA1::class);
    }


    public function findChoice1WithoutChoice2($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.draw = :val')
            ->andWhere('r.choiceOfa2 IS NULL')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }    // /**
    //  * @return ChoiceOFA1[] Returns an array of ChoiceOFA1 objects
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

    /*
    public function findOneBySomeField($value): ?ChoiceOFA1
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
