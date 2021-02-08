<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }


    /**
     * @return Notification[]
     * @throws \Exception
     */
    public function findActiveNotifications($options = []): array
    {
        return $this->searchNotifications($options, 'active');
    }

    /**
     * @return Notification[]
     * @throws \Exception
     */
    public function findActiveAndPendingNotifications($options = []): array
    {

    }

    public function findPending(array $options = []): array
    {
        return $this->searchNotifications($options, 'pending');
    }

    public function findByStartDate(string $start, string $end)
    {
        $dql = /** @lang DQL */
            <<< DQL
SELECT n FROM App\Entity\Notification n
WHERE (n.start > :start)
AND (n.start < :end)
ORDER BY n.start ASC 
DQL;
        $query = $this->getEntityManager()->createQuery($dql);
        $start_obj = new \DateTime($start, new \DateTimeZone('America/New_York'));
        $end_obj = new \DateTime($end, new \DateTimeZone('America/New_York'));
        $query->setParameter('start', $start_obj);
        $query->setParameter('end', $end_obj);
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
     * @throws \Exception
     */
    public function findActiveAutoposts()
    {
        $dql = <<<DQL
SELECT n FROM App\Entity\Notification n
WHERE n.autoposted = 1
AND n.start < :now
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

    /**
     * @param array $options
     * @param string $status
     * @return mixed
     * @throws \Exception
     */
    private function searchNotifications(array $options, string $status): array
    {
        $default_options = [
            'type' => null,
            'priority' => null,
            'application' => null
        ];
        $options = array_merge($default_options, $options);

        $now = new \DateTimeZone('America/New_York');

        $builder = $this->createQueryBuilder('n')
            ->orderBy('n.start');

        if ($status === 'pending') {
            $builder->andWhere('n.start > :now')
                ->andWhere('n.finish IS NULL OR n.finish > :now')
                ->setParameter('now', new \DateTime('now', $now));

        } elseif ($status === 'active') {
            $builder->andWhere('n.start < :now')
                ->andWhere('n.finish IS NULL OR n.finish > :now')
                ->setParameter('now', new \DateTime('now', $now));
        } elseif ($status === 'active_or_pending') {
            $builder->andWhere('n.finish IS NULL OR n.finish > :now')
                ->setParameter('now', new \DateTime('now', $now));
        }

        if ($options['type']) {
            $builder->join('n.type', 't')
                ->andWhere('t.name = :type')
                ->setParameter('type', $options['type']);
        }

        if ($options['priority']) {
            $builder->join('n.priority', 'p')
                ->andWhere('p.name = :priority')
                ->setParameter('priority', $options['priority']);
        }

        if ($options['application']) {
            $builder->join('n.application', 'a')
                ->andWhere('a.name = :app')
                ->setParameter('app', $options['application']);
        }

        return $builder->getQuery()
            ->execute();
    }
}
