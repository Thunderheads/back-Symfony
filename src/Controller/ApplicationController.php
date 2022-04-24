<?php

namespace App\Controller;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
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
    public function post(Request $req): Response
    {
        $sortieRequest = json_decode($req->getContent());

        //A DEBLOQUER QUAND TEST AVEC LA VRAI
        //$urlATester = $sortieRequest->urlATester;
        //$isInsert = $sortieRequest->isInsert ;

        $urlIOSpourTest = "https://apps.apple.com/fr/app/leboncoin/id484115113";
        $urlAndroidpourTest = "https://play.google.com/store/apps/details?id=fr.leboncoin&hl=fr&gl=US";

        $id = $this->getAndroidID($urlAndroidpourTest);
        $apple = "apps.apple.com";




        // OBTENTION DONNEE ANDROID
        // try catch car en cas de mauvais id on passera dans le catch qui renverra une requete avec un code d'erreur 404
        try {
            $scraper = new Scraper();
            $app = $scraper->getApp($id);
            //return $this->json($app,200, [],['groups'=>'application']);
        } catch (NotFoundException){
            //return $this->json($sortieRequest,404, [],['groups'=>'application']);
        }


        //obtention données APPLE
        // Apple

        $classCssNoteAppleStore = ".we-customer-ratings__averages__display";
        $classCssTitreAppleSore = ".product-header__title";
        $classCssNbAvis =".we-customer-ratings__count";
        //pour voir le rendu html
        //$data = file_get_contents($urlandroid);
        $client = new Client();
        $crawler = $client->request('GET', $urlIOSpourTest);
        //avec ça on récupere une note sur le store d'apple comment c'est du HTML avec goutte ça marche
        $app_nom = $crawler->filter($classCssTitreAppleSore)->innerText();
        $app_note = $crawler->filter($classCssNoteAppleStore)->innerText();

        //nombre avis en k
        $app_nombreAvis = $crawler->filter($classCssNbAvis)->innerText();

        dd($app_nombreAvis);




        // identifier le store

        // Tester l'url
        //si on doit inserer en base de données
        if($isInsert){

            // recuperer la note de l'application et la date du jour
            // recuperer l'os
            // recuperer l'url
            // inserer l'app en base de données
            // tout ça doit etre fait a partir d'une fonction dans le repo ... pas ici !!!!
        }


        return $this->json($sortieRequest,200, [],['groups'=>'application']);

    }

    /**
     * Fonction en charge de récuperer l'id de l'application contenu dans l'URL
     *
     * @param String $urlAndroid
     * @return String $id de l'application
     */
    function getAndroidID(String $urlAndroid) : String {

        // +1 pour ne pas inclure le =
        $positionDepart = strpos($urlAndroid, "=") + 1;
        $subString = substr("$urlAndroid", $positionDepart);
        $positionArrivee =  strpos($subString, "&") ;
        $tailleSequence = strlen($subString);
        $id = substr("$subString",0, $positionArrivee- $tailleSequence);

        return $id;
    }

}
