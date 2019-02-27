<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Notification::class);
    }


    /**
     * @return Notification[]
     * @throws \Exception
     */
    public function findActiveNotifications(): array
    {
        $dql = /** @lang DQL */
            <<< DQL
SELECT n FROM App\Entity\Notification n
WHERE n.start < :now
AND  (n.finish IS NULL OR n.finish > :now)
ORDER BY n.start ASC 
DQL;

        $query = $this->getEntityManager()->createQuery($dql);
        $now = new \DateTime('now', new \DateTimeZone('America/New_York'));
        $query->setParameter('now', $now);
        return $query->getResult();

    }

    /**
     * @return Notification[]
     * @throws \Exception
     */
    public function findPendingNotifications(): array
    {
        $dql = /** @lang DQL */
            <<< DQL
SELECT n FROM App\Entity\Notification n
WHERE n.start > :now
ORDER BY n.start ASC 
DQL;

        $query = $this->getEntityManager()->createQuery($dql);
        $now = new \DateTime('now', new \DateTimeZone('America/New_York'));
        $query->setParameter('now', $now);
        return $query->getResult();

    }

    /**
     * @return Notification[]
     * @throws \Exception
     */
    public function findActiveAndPendingNotifications(): array
    {
        $dql = /** @lang DQL */
            <<< DQL
SELECT n FROM App\Entity\Notification n
WHERE (n.finish IS NULL OR n.finish > :now)
ORDER BY n.start ASC 
DQL;

        $query = $this->getEntityManager()->createQuery($dql);
        $now = new \DateTime('now', new \DateTimeZone('America/New_York'));
        $query->setParameter('now', $now);
        return $query->getResult();

    }

    /**
     * @return Notification[]
     * @throws \Exception
     */
    public function findPending(): array
    {
        $dql = /** @lang DQL */
            <<< DQL
SELECT n FROM App\Entity\Notification n
WHERE (n.start > :now)
AND (n.finish IS NULL OR n.finish > :now)
ORDER BY n.start ASC 
DQL;

        $query = $this->getEntityManager()->createQuery($dql);
        $now = new \DateTime('now', new \DateTimeZone('America/New_York'));
        $query->setParameter('now', $now);
        return $query->getResult();
    }

    /**
     * @throws \Exception
     */
    public function findClosedQuery()
    {
        $dql = /** @lang DQL */
            <<< DQL
SELECT n FROM App\Entity\Notification n
WHERE (n.finish IS NOT NULL AND n.finish < :now)
ORDER BY n.start ASC 
DQL;

        $query = $this->getEntityManager()->createQuery($dql);
        $now = new \DateTime('now', new \DateTimeZone('America/New_York'));
        $query->setParameter('now', $now);
        return $query;
    }

    public function searchQuery(string $term): \Doctrine\ORM\Query
    {
        $dql = <<<DQL
SELECT n FROM App\Entity\Notification n 
WHERE (n.text LIKE :term)
ORDER BY n.start DESC
DQL;
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('term', "%$term%");
        return $query;
    }

    /**
     * @return Notification[]
     */
    public function findAll(): array
    {
        return $this->findBy([], ['start' => 'DESC']);
    }

    public function findAllQuery()
    {
        $dql = /** @lang DQL */
            <<< DQL
SELECT n FROM App\Entity\Notification n
ORDER BY n.start DESC 
DQL;
        return $this->getEntityManager()->createQuery($dql);
    }
}
