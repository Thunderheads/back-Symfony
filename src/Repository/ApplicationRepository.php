<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Application $entity, bool $flush = true): void
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
    public function remove(Application $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     *
     * Fonction en charge de recuperer les données deux derniers jours
     *
     * @return Application[] Returns an array of Application objects
     */

    public function findTwoLastDayData()
    {
        $now = (new \DateTime('now'))->format('Y-m-d');
        $yesterday = (new \DateTime('-1 day'))->format('Y-m-d');

        return $this->createQueryBuilder('a')
            ->innerJoin('a.datas', 'd')
            ->where('d.dateCollect > :now')
            ->setParameter(':now', new \DateTime('2022-04-10'))
            //->setParameter(':now', new \DateTime('2021-04-19'))
            //->andWhere('d.dateCollect = :yesterday')
            //->setParameter(':yesterday', new \DateTime($yesterday))
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function test()
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql =
            'SELECT A.*, B.vote vote_hier, (A.vote - B.vote) diff
                FROM ( SELECT android.* , iOS.plateforme2 FROM (
                SELECT application.id, application.nom nom_application, rating, vote,  CONCAT(responsable.nom,:vide, responsable.prenom) responsable, os.nom plateforme1 FROM `application`
 INNER JOIN `donnes` ON donnes.application_id = application.id
 INNER JOIN `responsable` ON responsable.id = application.administrateur_id
 INNER JOIN `source` ON source.application_id = application.id
 INNER JOIN `os` ON os.id = source.os_id
 
 WHERE donnes.date_collect = :yesterday AND os.nom = :android GROUP BY donnes.id) AS android
JOIN ( SELECT application.id, application.nom , rating, vote,  CONCAT(responsable.nom, responsable.prenom) responsable, os.nom  plateforme2 FROM `application`
 INNER JOIN `donnes` ON donnes.application_id = application.id
 INNER JOIN `responsable` ON responsable.id = application.administrateur_id
 INNER JOIN `source` ON source.application_id = application.id
 INNER JOIN `os` ON os.id = source.os_id
 
 WHERE donnes.date_collect = :yesterday AND os.nom = :iOS GROUP BY donnes.id
) AS iOS ON android.id = iOS.id
) AS A
JOIN ( SELECT android.* , iOS.plateforme2 FROM (
SELECT application.id , application.nom , rating, vote,  CONCAT(responsable.nom, :vide ,responsable.prenom) responsable, os.nom plateforme1 FROM `application`
 INNER JOIN `donnes` ON donnes.application_id = application.id
 INNER JOIN `responsable` ON responsable.id = application.administrateur_id
 INNER JOIN `source` ON source.application_id = application.id
 INNER JOIN `os` ON os.id = source.os_id
 
 WHERE donnes.date_collect = :now AND os.nom = :android GROUP BY donnes.id) AS android
JOIN ( SELECT application.id, application.nom , rating, vote,  CONCAT(responsable.nom, responsable.prenom) responsable, os.nom  plateforme2 FROM `application`
 INNER JOIN `donnes` ON donnes.application_id = application.id
 INNER JOIN `responsable` ON responsable.id = application.administrateur_id
 INNER JOIN `source` ON source.application_id = application.id
 INNER JOIN `os` ON os.id = source.os_id
 
 WHERE donnes.date_collect = :now AND os.nom = :iOS GROUP BY donnes.id
) AS iOS ON android.id = iOS.id
) AS B
ON A.id = B.id' ;
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['now' => '2022-04-17 00:00:00', 'yesterday' => '2022-04-18 00:00:00', 'iOS' => 'iOS', 'android' => 'android', 'vide' => ' ']);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    /*
    public function findOneBySomeField($value): ?Application
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
