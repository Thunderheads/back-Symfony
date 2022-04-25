<?php

namespace App\Repository;

use App\Entity\Application;
use App\Entity\Donnes;
use App\Entity\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
    private OSRepository $osRepository;

    public function __construct(ManagerRegistry $registry, OSRepository $osRepository)
    {
        $this->osRepository = $osRepository;
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


    public function test()
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql =
            'SELECT A.id app_id, A.nom app_nom, A.os, A.rating, A.vote vote_jour, (A.vote - B.vote) diff
FROM (
    SELECT application.id, application.nom, os.nom os, donnes.date_collect, donnes.vote, donnes.rating FROM `application` INNER JOIN donnes ON donnes.application_id = application.id
INNER JOIN `os` ON os.id = donnes.os_id
INNER JOIN `responsable` ON responsable.id = application.administrateur_id
WHERE donnes.date_collect = :now
GROUP BY donnes.id
) AS A

JOIN (
    SELECT application.id, application.nom, os.nom os, donnes.date_collect, donnes.vote, donnes.rating FROM `application` INNER JOIN donnes ON donnes.application_id = application.id
INNER JOIN `os` ON os.id = donnes.os_id
INNER JOIN `responsable` ON responsable.id = application.administrateur_id
WHERE donnes.date_collect = :yesterday
GROUP BY donnes.id
) AS B
ON A.id = B.id AND A.os = B.os;' ;
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['now' => '2022-04-17 00:00:00', 'yesterday' => '2022-04-18 00:00:00']);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }


    public function addApplication($information , $applicationNom, $urlATester) {
        // instancier des objects

        $donnes = new Donnes();
        $application = new Application();
        $source = new Source();
        $os = $this->osRepository->findOneBy(["nom"=>$information['app_os']]);

        // remplir les objets
        //$application->addData();
        if($applicationNom != 'undefined'){
            $application->setNom($applicationNom);
        } else {
            $application->setNom($information['app_nom']);
        }

        $this->getEntityManager()->persist($application);
        $this->getEntityManager()->flush();



        $donnes->setOs($os);
        $donnes->setVote($information["app_nombreAvis"]);
        $donnes->setDateCollect(new \DateTime((new \DateTime('now'))->format('Y-m-d')));
        $donnes->setRating((float)($information["app_note"]));
        $donnes->setApplication($application);
        $this->getEntityManager()->persist($donnes);
        $this->getEntityManager()->flush();



        $source->setOs($os);
        $source->setApplication($application);
        $source->setUrl($urlATester);

        $this->getEntityManager()->persist($source);
        $this->getEntityManager()->flush();


        return $application;
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
