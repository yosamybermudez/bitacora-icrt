<?php

namespace App\Repository;

use App\Entity\TareaEspecifica;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TareaEspecifica>
 *
 * @method TareaEspecifica|null find($id, $lockMode = null, $lockVersion = null)
 * @method TareaEspecifica|null findOneBy(array $criteria, array $orderBy = null)
 * @method TareaEspecifica[]    findAll()
 * @method TareaEspecifica[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TareaEspecificaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TareaEspecifica::class);
    }

    public function add(TareaEspecifica $entity, Usuario $usuario, bool $flush = false): void
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

    public function remove(TareaEspecifica $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findTareasPendientes(Usuario $usuario = null, bool $full_access){ //Todas las tareas pendientes, sin importar usuario ni area

        if(!$full_access && $usuario){
            $area = $usuario->getTrabajador()->getArea();
            $tareas_pendientes_area = $this->createQueryBuilder('te')
                ->innerJoin('te.tarea', 't')
                ->innerJoin('t.areas', 'a')
                ->leftJoin('te.asignado_a', 'u')
                ->where('te.estado != \'solucionada\'')
                ->andWhere('a.id = :area_id')
                ->setParameter('area_id', $area->getId())
                ->having('COUNT(u) = 0')
                ->groupBy('te')
                ->getQuery()
                ->getResult();

            $tareas_pendientes_usuario = $this->createQueryBuilder('te')
                ->innerJoin('te.tarea', 't')
                ->innerJoin('t.areas', 'a')
                ->innerJoin('te.asignado_a', 'u')
                ->where('te.estado != \'solucionada\'')
                ->andWhere('a.id = :area_id')
                ->andWhere('u.id = :usuario_id')
                ->setParameter('area_id', $area->getId())
                ->setParameter('usuario_id', $usuario->getId())
                ->getQuery()
                ->getResult();

            return array_merge($tareas_pendientes_area, $tareas_pendientes_usuario);
        } else {
            $qb = $this->createQueryBuilder('te');
            $qb->where('te.estado != :estado')
                ->setParameter('estado', 'solucionada');
            return $qb->getQuery()->getResult();
        }

    }


//    /**
//     * @return TareaEspecifica[] Returns an array of TareaEspecifica objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TareaEspecifica
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
