<?php

namespace App\Repository;

use App\Entity\BestBet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BestBet|null find($id, $lockMode = null, $lockVersion = null)
 * @method BestBet|null findOneBy(array $criteria, array $orderBy = null)
 * @method BestBet[]    findAll()
 * @method BestBet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BestBetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BestBet::class);
    }

    public function findAllQuery()
    {
        $dql = /** @lang DQL */
            <<< DQL
SELECT b FROM App\Entity\BestBet b
ORDER BY b.needs_update DESC,b.title ASC 
DQL;

        $query = $this->getEntityManager()->createQuery($dql);
        return $query;
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
