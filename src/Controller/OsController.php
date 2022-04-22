<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Donnes;
use App\Entity\OS;
use App\Repository\OSRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OsController extends AbstractController
{
    #[Route('/api/os/{id}', name: 'app_os')]
    public function index(OSRepository $OSRepository, $id): Response
    {
        $applicationIOS = new Application();
        $applicationANDROID = new Application();
        $android = new OS();
        $ios = new OS();
        $ios->setNom('iOS');
        $lstOS = new ArrayCollection();
        $android->setNom('android');
        foreach ($OSRepository->findByAppID($id) as $item ){
            //si android
            if($item['os_id'] == "1"){
                $data = new Donnes();
                $data->setVote($item['vote'])
                    ->setRating($item['rating'])
                    ->setDateCollect(new \DateTime($item['date_collect']))
                    ->setApplication($applicationANDROID->setNom($item['nom']));
                $android->addDonne($data);
            } else {
                //si ios
                $data = new Donnes();
                $data->setVote($item['vote'])
                    ->setRating($item['rating'])
                    ->setDateCollect(new \DateTime($item['date_collect']))
                    ->setApplication($applicationIOS->setNom($item['nom']));
                $ios->addDonne($data);
            }
    }
        $lstOS->add($android);
        $lstOS->add($ios);
        return $this->json($lstOS,200, [],['groups'=>'os']);
    }
}
