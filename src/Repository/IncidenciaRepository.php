<?php

namespace App\Repository;

use App\Entity\Area;
use App\Entity\Incidencia;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Incidencia>
 *
 * @method Incidencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Incidencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Incidencia[]    findAll()
 * @method Incidencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncidenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Incidencia::class);
    }

    public function add(Incidencia $entity, Usuario $usuario, bool $flush = false): void
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

    public function remove(Incidencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Incidencia[] Returns an array of Incidencia objects
     */
    public function findIncidenciasNoSolucionadas(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.estado != :val')
            ->setParameter('val', 'Solucionado')
            ->orderBy('i.actualizadoEn', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Incidencia[] Returns an array of Incidencia objects
     */
    public function findIncidenciasNoSolucionadasPorArea(Usuario $usuario, bool $full_access): array
    {
        $qb = $this->createQueryBuilder('i');

        if(!$full_access){
            $qb->innerJoin('i.areas', 'a', Join::WITH, 'a.id = :area')
                ->setParameter('area', $usuario->getTrabajador()->getArea()->getId());
        }

        return $qb->where('i.estado != :val')
            ->setParameter('val', 'solucionada')
            ->orderBy('i.actualizadoEn', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Incidencia[] Returns an array of Incidencia objects
     */
    public function findIncidenciasPorArea(Usuario $usuario, bool $full_access): array
    {
        $qb = $this->createQueryBuilder('i');
        if(!$full_access){
            $qb->innerJoin('i.areas', 'a', Join::WITH, 'a.id = :area')
                ->setParameter('area', $usuario->getTrabajador()->getArea()->getId());
        }

        return $qb
            ->orderBy('i.actualizadoEn', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return Incidencia[] Returns an array of Incidencia objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Incidencia
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
