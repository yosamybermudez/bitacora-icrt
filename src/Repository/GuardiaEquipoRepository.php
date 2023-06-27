<?php

namespace App\Repository;

use App\Entity\GuardiaEquipo;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RRule\RRule;

/**
 * @extends ServiceEntityRepository<GuardiaEquipo>
 *
 * @method GuardiaEquipo|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuardiaEquipo|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuardiaEquipo[]    findAll()
 * @method GuardiaEquipo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuardiaEquipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuardiaEquipo::class);
    }

    public function add(GuardiaEquipo $entity, Usuario $usuario, bool $flush = false): void
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

    public function remove(GuardiaEquipo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findEquipoGuardiaHoy(){
        $equipos = $this->findAll();
        foreach ($equipos as $equipo){
            $rrule = new RRule($equipo->getRecurrencia());
//
            $ocurrence  = $rrule->getNthOccurrenceFrom('now', -1);
            if(date_format(new \DateTime(), 'Y-m-d') === date_format($ocurrence, 'Y-m-d')){
                return $equipo;
            }
        }
        return null;
    }

//    /**
//     * @return GuardiaEquipo[] Returns an array of GuardiaEquipo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GuardiaEquipo
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
