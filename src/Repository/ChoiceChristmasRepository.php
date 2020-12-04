<?php

namespace App\Repository;

use App\Entity\Choice;
use App\Entity\ChoiceChristmas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Choice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Choice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Choice[]    findAll()
 * @method Choice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoiceChristmasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChoiceChristmas::class);
    }

    public function findByRankingNoPlace($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.christmas = :val')
            ->andWhere('r.giftTo IS NULL')
            ->setParameter('val', $value)
            ->orderBy('r.giftTo', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByChoiceNotHimSelfAndWithoutGiftTo($value,$value2)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.christmas = :val')
            ->andWhere('r.giftTo IS NULL')
            ->andWhere('r.text != :val2')
            ->setParameter('val', $value)
            ->setParameter('val2', $value2)
            ->orderBy('r.giftTo', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
