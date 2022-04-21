<?php

namespace App\Repository;

use App\Entity\Donnes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Donnes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donnes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donnes[]    findAll()
 * @method Donnes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonnesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donnes::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Donnes $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Donnes $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Donnes[] Returns an array of Donnes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Donnes
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /*
    public function findTwoLastDayData()
    {
        $now =  (new \DateTime('now'))->format('Y-m-d');
        $yesterday = (new \DateTime('-1 day'))->format('Y-m-d');

        return $this->createQueryBuilder('d')
            ->select('d')
            ->where('d.dateCollect = :now')
            ->setParameter(':now', '2022-03-17 00:00:00')
            ->orWhere('d.dateCollect = :yesterday')
            ->setParameter(':yesterday', '2022-03-18 00:00:00')
            //->setParameter(':now', new \DateTime('2021-04-19'))
            //->andWhere('d.dateCollect = :yesterday')
            //->setParameter(':yesterday', new \DateTime($yesterday))

            ->getQuery()

            ->getResult()
            ;
    }
    */
}
