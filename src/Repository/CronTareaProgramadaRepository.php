<?php

namespace App\Repository;

use App\Entity\CronTareaProgramada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CronTareaProgramada>
 *
 * @method CronTareaProgramada|null find($id, $lockMode = null, $lockVersion = null)
 * @method CronTareaProgramada|null findOneBy(array $criteria, array $orderBy = null)
 * @method CronTareaProgramada[]    findAll()
 * @method CronTareaProgramada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CronTareaProgramadaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronTareaProgramada::class);
    }

    public function add(CronTareaProgramada $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CronTareaProgramada $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CronTareaProgramada[] Returns an array of CronTareaProgramada objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CronTareaProgramada
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
