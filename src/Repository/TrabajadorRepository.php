<?php

namespace App\Repository;

use App\Entity\Trabajador;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trabajador>
 *
 * @method Trabajador|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trabajador|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trabajador[]    findAll()
 * @method Trabajador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrabajadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trabajador::class);
    }

    public function add(Trabajador $entity, Usuario $usuario, bool $flush = false): void
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

        if($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trabajador $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllCumpleannos(){
        $trabajadores = $this->findAll();

        $cumpleannos = [];
        foreach ($trabajadores as $trabajador){
            $fecha = $this->getFechaNacimiento($trabajador->getCarneIdentidad());
            if(!$fecha){
                continue;
            }
            $cumpleannos[] = [
                'trabajador' => $trabajador->getNombreCorto(),
                'fecha' => $fecha
            ];
        }
        return $cumpleannos;
    }

    public function getFechaNacimiento($carne_identidad)
    {
        $regexp = '/[0-9]{2}(?:0[0-9]|1[0-2])(?:0[1-9]|[12][0-9]|3[01])[0-9]{5}/';
        if(!preg_match($regexp, $carne_identidad))
        {
            return false;
        }
        return $this->isValidDate($carne_identidad);
    }

    public function isValidDate($carne_identidad)
    {
        $y = substr($carne_identidad,0,2);
        $m = substr($carne_identidad,2,2);
        $d = substr($carne_identidad,4,2);
        $date = \DateTime::createFromFormat('y-m-d', sprintf('%s-%s-%s', $y, $m, $d));
        $date->setTime(0,0,0);
        return $date;
    }

        /**
     * @return Trabajador[] Returns an array of Trabajador objects
     */
    public function findTrabajadoresTelegram(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.telegram_id is not null')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Trabajador[] Returns an array of Trabajador objects
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

//    public function findOneBySomeField($value): ?Trabajador
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
