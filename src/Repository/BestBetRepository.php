<?php

namespace App\Repository;

use App\Entity\BestBet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BestBet|null find($id, $lockMode = null, $lockVersion = null)
 * @method BestBet|null findOneBy(array $criteria, array $orderBy = null)
 * @method BestBet[]    findAll()
 * @method BestBet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BestBetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BestBet::class);
    }

    // /**
    //  * @return BestBet[] Returns an array of BestBet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BestBet
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
