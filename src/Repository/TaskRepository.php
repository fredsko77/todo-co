<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param int $page
     * @param int $itemsPerPage
     */
    public function findTasks(int $page = 0, int $itemsPerPage = 30, array $criteria = [], string $orderBy = 'ASC')
    {

        $query = $this->createQueryBuilder('t')
            ->orderBy("t.createdAt", $orderBy)
        ;

        foreach ($criteria as $k => $c) {
            $query->where("t.{$k} = $c");
        }

        return $query;
    }

    public function countTasks(int $page = 0, int $itemsPerPage = 30, array $criteria = [])
    {
        return count($this->findTasks($page, $itemsPerPage, $criteria)
                ->getQuery()
                ->getResult()
        );
    }

    public function findPaginatedTasks(int $page = 0, int $itemsPerPage = 30, array $criteria = [], string $orderBy = 'ASC')
    {
        $starter = ($page * $itemsPerPage);

        return $this->findTasks($page, $itemsPerPage, $criteria, $orderBy)
            ->setFirstResult($starter)
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUnanonymous(int $isDone = 0): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user_id <> NULL')
            ->andWhere('t.is_done = :isDone')
            ->setParameter('idDone', $isDone)
            ->getFirstResult()
        ;
    }

    public function findByUser(int $isDone = 0): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user_id = NULL')
            ->andWhere('t.is_done = :isDone')
            ->setParameter('idDone', $isDone)
            ->getFirstResult()
        ;
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
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
public function findOneBySomeField($value): ?Task
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
