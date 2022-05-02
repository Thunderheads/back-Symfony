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

    public function test($id = 0, $ordre = null)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql =  $sql =
            'SELECT A.id app_id, A.nom app_nom, A.os, A.rating, A.vote vote_jour, (COALESCE(A.vote, 0) - COALESCE(B.vote, 0)) diff 
                FROM ( 
                    SELECT application.id, application.nom, os.nom os, donnes.date_collect, donnes.vote, donnes.rating FROM `application` LEFT JOIN ( 
                        SELECT * FROM donnes WHERE donnes.date_collect = :now ) 
                        AS donnes ON donnes.application_id = application.id LEFT JOIN `os` ON os.id = donnes.os_id ) 
                    AS A LEFT JOIN ( SELECT application.id, application.nom, os.nom os, donnes.date_collect, donnes.vote, donnes.rating FROM `application` 
                        LEFT JOIN ( SELECT * FROM donnes WHERE donnes.date_collect = 
            :yesterday) AS donnes ON donnes.application_id = application.id LEFT JOIN `os` ON os.id = donnes.os_id )  AS B ON A.id = B.id AND A.os = B.os ';


        // cas id mais pas ordre
        if($id != 0 && $ordre == null){

            $sql = $sql . ' WHERE A.id = :id;';
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['now' => (new \DateTime((new \DateTime('now'))->format('Y-m-d')))->format('Y-m-d H:i:s'), 'yesterday' => ((new \DateTime((new \DateTime('now'))->format('Y-m-d')))->modify("-1 day"))->format('Y-m-d H:i:s'), 'id' => $id]);

            // returns an array of arrays (i.e. a raw data set)
            return $resultSet->fetchAllAssociative();
        }

        // cas ordre mais pas id
        if($id == 0 && $ordre != null){

            if($ordre == 'croissant'){
                $sql = $sql . ' ORDER BY rating ;';
            }else {
                $sql = $sql . ' ORDER BY rating DESC;';
            }
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['now' => (new \DateTime((new \DateTime('now'))->format('Y-m-d')))->format('Y-m-d H:i:s'), 'yesterday' => ((new \DateTime((new \DateTime('now'))->format('Y-m-d')))->modify("-1 day"))->format('Y-m-d H:i:s')]);

            // returns an array of arrays (i.e. a raw data set)
            return $resultSet->fetchAllAssociative();
        }

        // cas les deux
        if($id != 0 && $ordre != null){
            $sql = $sql . ' WHERE A.id = :id ';
            if($ordre == 'croissant'){
                $sql = $sql . 'ORDER BY rating ;';
            }else {
                $sql = $sql . 'ORDER BY rating DESC;';
            }

            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['now' => (new \DateTime((new \DateTime('now'))->format('Y-m-d')))->format('Y-m-d H:i:s'), 'yesterday' =>((new \DateTime((new \DateTime('now'))->format('Y-m-d')))->modify("-1 day"))->format('Y-m-d H:i:s'), 'id' => $id]);

            // returns an array of arrays (i.e. a raw data set)
            return $resultSet->fetchAllAssociative();
        }

        $sql = $sql . ';';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['now' =>  (new \DateTime((new \DateTime('now'))->format('Y-m-d')))->format('Y-m-d H:i:s'), 'yesterday' => ((new \DateTime((new \DateTime('now'))->format('Y-m-d')))->modify("-1 day"))->format('Y-m-d H:i:s') ]);

        // returns an array of arrays (i.e. a raw data set)
        $sql = $sql."ORDER BY os DESC;";
        return $resultSet->fetchAllAssociative();


    }


    /**
     * Fonction en charge d'ajouter une application et de recuperer la donnée actuelle
     *
     * @param $information
     * @param $applicationNom
     * @param $urlATester
     * @return Application
     * @throws \Exception
     */
    public function addApplication($information , $applicationNom, $urlATester) {
        // instancier des objects

        $donnes = new Donnes();
        $application = new Application();
        $source = new Source();
        $os = $this->osRepository->findOneBy(["nom"=>$information['app_os']]);

        // remplir les objets
        //$application->addData();
        if($applicationNom != 'undefined'){
            // si l'application existe deja
            if( !is_null(($this->findOneBy(["nom" => $applicationNom])))){
                $application = $this->findOneBy(["nom" => $applicationNom]);
            } else{
                $application->setNom($applicationNom);
            }


        } else {
            if( !is_null($this->findOneBy(["nom" => $information['app_nom']])) ){
                $application = $this->findOneBy(["nom" => $information['app_nom']]);
            } else{
                $application->setNom($information['app_nom']);
            }
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


    /**
     * Fonction en charge de mettre a jour les urls et le nom d'une application
     *
     * @param $body
     * @return Application|null
     */
    public function updateApplication($body){

        $applicationToUpdate = $this->find($body->id);
        $applicationToUpdate->setNom($body->nom);

        foreach ($body->sources as $newSource){
            foreach ($applicationToUpdate->getSources() as $oldSource){
                if($oldSource->getOs()->getNom() == $newSource->os->nom){
                    $oldSource->setUrl($newSource->url);
                }
            }
        }
        $this->getEntityManager()->flush();

        return $applicationToUpdate;
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


    /**
     * Renvoie la liste des applications triées par leur nom
     *
     * @return Application[]|array|object[]
     */
    public function findAll(){
        return $this->findBy(array(), array('nom' => 'ASC'));
    }

}
