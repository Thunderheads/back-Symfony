<?php

namespace App\Controller;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use App\Service\Scrapping;
use Goutte\Client;
use Raulr\GooglePlayScraper\Exception\NotFoundException;
use Raulr\GooglePlayScraper\Scraper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    #[Route('/api/application', name: 'application_all_get', methods: ['GET'])]
    public function getAll(ApplicationRepository $applicationRepo): Response
    {
        //pour recuperer les dates du jour dd((new \DateTime())->format('Y-m-d'));

        return $this->json($applicationRepo->test(),200, [],['groups'=>'application']);
    }

    #[Route('/api/application/{id}', name: 'application_id_get', methods: ['GET'])]
    public function getID(Application $application): Response
    {
        return $this->json($application,200, [],['groups'=>'application']);

    }

    #[Route('/api/application/', name: 'application_post', methods: ['POST'])]
    public function post(Request $req, Scrapping $scrapping): Response
    {
        $sortieRequest = json_decode($req->getContent());

        //A DEBLOQUER QUAND TEST AVEC LA VRAI
        $urlATester = $sortieRequest->urlATester;
        $isInsert = $sortieRequest->isInsert ;
        $nomApp =  $sortieRequest->nomApplication;

        //$urlIOSpourTestmarche = "https://apps.apple.com/fr/app/leboncoin/id484115113?";
        //$urlIOSpourTestmarchepas = "https://apps.apple.com/fr/app/leboncoin/id48411511éééééééeée3?";
        //$urlAndroidpourTest = "https://play.google.com/store/apps/details?id=fr.leboncoin&hl=fr&gl=US";
        //$urlAndroidpourTestmarchePAs = "https://play.google.com/store/apps/details?id=fr.leboncoincoin&hl=fr&gl=USgfdgfgd";

        if($isInsert){
            $data = $scrapping->insertApp($urlATester, $nomApp);
            if($data != null){
                return $this->json($data,200, []);
            } else {
                return $this->json($data,404, []);
            }
        } else{
            $data = $scrapping->getInformation($urlATester);
            if($data != null){
                return $this->json($data,200, []);
            } else {
                return $this->json($data,404, []);
            }
        }
    }



}
