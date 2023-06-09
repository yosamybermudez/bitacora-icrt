<?php

namespace App\Repository;

use App\Entity\Area;
use App\Entity\TareaRecurrencia;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RRule\RRule;

/**
 * @extends ServiceEntityRepository<TareaRecurrencia>
 *
 * @method TareaRecurrencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method TareaRecurrencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method TareaRecurrencia[]    findAll()
 * @method TareaRecurrencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TareaRecurrenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TareaRecurrencia::class);
    }

    public function add(TareaRecurrencia $entity, Usuario $usuario, bool $flush = false): void
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

    public function remove(TareaRecurrencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findTareasAreaHoy(Usuario $usuario, bool $full_access){
        $tareas_area = $this->findTareasArea($usuario, $full_access);
        $tareas_periodicas = [];
        foreach ($tareas_area as $tarea){
            $rrule = new RRule($tarea->getRecurrencia());
            $ocurrence  = $rrule->getNthOccurrenceFrom(new \DateTime(), -1);
            if(date_format(new \DateTime(), 'Y-m-d') === date_format($ocurrence, 'Y-m-d')){
                $tareas_periodicas[] = $tarea;
            }

        }
        return $tareas_periodicas;
    }

    public function findTareasArea(Usuario $usuario, bool $full_access){
        $qb = $this->createQueryBuilder('tr');
        $qb->innerJoin('tr.tarea', 't');
        if(!$full_access){
            $qb
                ->innerJoin('t.areas', 'a')
                ->where('a.id = :area_id')
                ->setParameter('area_id', $usuario->getTrabajador()->getArea()->getId());
        }

        $result =
            $qb
                ->getQuery()
                ->getResult()
        ;
        return $result;
    }

    public function findTareasPorArea(Area $area){
        $qb = $this->createQueryBuilder('tr');
        $qb->innerJoin('tr.tarea', 't');

                $qb
                    ->innerJoin('t.areas', 'a')
                    ->where('a.id = :area_id')
                    ->setParameter('area_id', $area->getId());


            $result =
                $qb
                ->getQuery()
                ->getResult()
            ;
            return $result;
    }

    public function findTareasPorAreaHoy(Area $area){
        $tareas = $this->findTareasPorArea($area);
        $tareas_periodicas = [];
        foreach ($tareas as $tarea){
            $rrule = new RRule($tarea->getRecurrencia());
            $ocurrence  = $rrule->getNthOccurrenceFrom(new \DateTime(), -1);
            if(date_format(new \DateTime(), 'Y-m-d') === date_format($ocurrence, 'Y-m-d')){
                $tareas_periodicas[] = $tarea;
            }
        }
        return $tareas_periodicas;
    }

//    /**
//     * @return TareaRecurrencia[] Returns an array of TareaRecurrencia objects
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

//    public function findOneBySomeField($value): ?TareaRecurrencia
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
