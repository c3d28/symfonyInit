<?php

namespace App\Repository;

use App\Entity\ChoiceOFA2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChoiceOFA2|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChoiceOFA2|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChoiceOFA2[]    findAll()
 * @method ChoiceOFA2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoiceOFA2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChoiceOFA2::class);
    }

    public function findChoice2WithoutChoice1($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.draw = :val')
            ->andWhere('r.choiceOfa1 IS NULL')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return ChoiceOFA2[] Returns an array of ChoiceOFA2 objects
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
    public function findOneBySomeField($value): ?ChoiceOFA2
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
