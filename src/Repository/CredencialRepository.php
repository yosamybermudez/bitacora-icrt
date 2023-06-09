<?php

namespace App\Repository;

use App\Entity\Credencial;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Credencial>
 *
 * @method Credencial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Credencial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Credencial[]    findAll()
 * @method Credencial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CredencialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Credencial::class);
    }

    public function add(Credencial $entity, Usuario $usuario, bool $flush = false): void
    {
        if (is_callable(array($entity, 'getCreadoEn')) and is_callable(array($entity, 'setCreadoEn'))) {
            if ($entity->getCreadoEn() === null){
                $entity->setCreadoEn(new \DateTime());
            }
        }
        if (is_callable(array($entity, 'getCreadoPor')) and is_callable(array($entity, 'setCreadoPor'))) {
            if ($entity->getCreadoPor() === null){
                $entity->setCreadoPor($usuario);
            }
        }
        if (is_callable(array($entity, 'setActualizadoEn'))) {
            $entity->setActualizadoEn(new \DateTime());
        }
        if (is_callable(array($entity, 'setActualizadoPor'))) {
            $entity->setActualizadoPor($usuario);
        }
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Credencial $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Credencial[] Returns an array of Incidencia objects
     */
    public function findCredencialesPorArea(Usuario $usuario, bool $full_access): array
    {
        $qb = $this->createQueryBuilder('c');
        if(!$full_access){
            $qb->innerJoin('c.areas', 'a', Join::WITH, 'a.id = :area')
                ->setParameter('area', $usuario->getTrabajador()->getArea()->getId());
        }

        return $qb
            ->orderBy('c.destino', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return Credencial[] Returns an array of Credencial objects
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

//    public function findOneBySomeField($value): ?Credencial
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
