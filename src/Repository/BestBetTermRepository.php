<?php

namespace App\Repository;

use App\Entity\BestBetTerm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BestBetTerm|null find($id, $lockMode = null, $lockVersion = null)
 * @method BestBetTerm|null findOneBy(array $criteria, array $orderBy = null)
 * @method BestBetTerm[]    findAll()
 * @method BestBetTerm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BestBetTermRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BestBetTerm::class);
    }

    // /**
    //  * @return BestBetTerm[] Returns an array of BestBetTerm objects
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
    public function findOneBySomeField($value): ?BestBetTerm
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
