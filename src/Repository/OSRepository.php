<?php

namespace App\Repository;

use App\Entity\OS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OS|null find($id, $lockMode = null, $lockVersion = null)
 * @method OS|null findOneBy(array $criteria, array $orderBy = null)
 * @method OS[]    findAll()
 * @method OS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OSRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OS::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(OS $entity, bool $flush = true): void
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
    public function remove(OS $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByAppID($id){

        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM os INNER JOIN donnes as d ON d.os_id = os.id INNER JOIN application on application.id = d.application_id where application.id = :id';


        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id'=>$id]);

        return $resultSet->fetchAllAssociative();
    }
    // /**
    //  * @return OS[] Returns an array of OS objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OS
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
