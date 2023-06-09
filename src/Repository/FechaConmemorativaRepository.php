<?php

namespace App\Repository;

use App\Entity\FechaConmemorativa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FechaConmemorativa>
 *
 * @method FechaConmemorativa|null find($id, $lockMode = null, $lockVersion = null)
 * @method FechaConmemorativa|null findOneBy(array $criteria, array $orderBy = null)
 * @method FechaConmemorativa[]    findAll()
 * @method FechaConmemorativa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FechaConmemorativaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FechaConmemorativa::class);
    }

    public function add(FechaConmemorativa $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FechaConmemorativa $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FechaConmemorativa[] Returns an array of FechaConmemorativa objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FechaConmemorativa
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
