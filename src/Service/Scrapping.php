<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Donnes;
use App\Repository\ApplicationRepository;
use App\Repository\DonnesRepository;
use App\Repository\OSRepository;
use App\Repository\SourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Goutte\Client;
use InvalidArgumentException;
use Raulr\GooglePlayScraper\Exception\NotFoundException;
use Raulr\GooglePlayScraper\Scraper;

class Scrapping
{

    private ApplicationRepository $applicationRepository;
    private SourceRepository $sourceRepository;
    private DonnesRepository $donnesRepository;
    private OSRepository $oSRepository;

    public function __construct( ApplicationRepository $applicationRepository,
                                 SourceRepository $sourceRepository,
                                 DonnesRepository $donnesRepository,
    OSRepository $oSRepository
    )
    {
        $this->applicationRepository = $applicationRepository;
        $this->sourceRepository = $sourceRepository;
        $this->donnesRepository = $donnesRepository;
        $this->oSRepository = $oSRepository;
    }


    /**
     * Fonction en charge d'ajouter les données
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insertDailyData(){


        // on recupere toute les urls en bases de données
        $lstSources = $this->sourceRepository->findAllCustom();

        $stat = [];

        foreach ($lstSources as $source){
            $début = new \DateTime('now');

            // on lance la fonction getInformation sur ces urls
            $information = $this->getInformation($source['url']);

            // on crée des objet données que l'on rajoute à la bonne application on spécifiant la version (ios ou android)
            $donnes = new Donnes();
            $donnes->setApplication($this->applicationRepository->find($source['application_id']));
            $donnes->setOs($this->oSRepository->find($source['os_id']));
            $donnes->setRating((float)$information["app_note"]);
            $donnes->setDateCollect(new \DateTime((new \DateTime('now'))->format('Y-m-d')));
            $donnes->setVote($information["app_nombreAvis"]);
            $this->donnesRepository->add($donnes);
            $fin  = new \DateTime('now');

            $stat[$source['url']] = $début->diff($fin)->s.".".$début->diff($fin)->f;
        }
        return $stat;

    }

    public function testMultipleAppleRequest(){

        $compteur = 0;
        $liste = ["https://apps.apple.com/fr/app/instagram/id389801252",
            "https://apps.apple.com/fr/app/clash-of-clans/id529479190",
            "https://apps.apple.com/fr/app/fnac-achat-en-ligne/id377379474",
            "https://apps.apple.com/fr/app/facebook/id284882215",
            "https://apps.apple.com/fr/app/pacifica/id418531407"
            ];

        $statPartour = [];
        $tour = 0;
        while($tour<30){
            $tour++;
            $stat = [];
            foreach( $liste as $url){
                $compteur++;
                $début = new \DateTime('now');
                $information = $this->getInformation($url);
                $fin  = new \DateTime('now');
                $stat[$url] = $début->diff($fin)->s.".".$début->diff($fin)->f;
            }
            $statPartour[$tour] = $stat;
        }


        return $statPartour;
    }


    /**
     * Fonction en charge d'inserer une application en base de données
     *
     * @param $urlATester
     * @param $nomApplication
     * @return Application
     */
    public function insertApp($urlATester, $nomApplication)
    {
        // recuperation des informations de l'applications
        $information = $this->getInformation($urlATester);
        return $this->applicationRepository->addApplication($information, $nomApplication, $urlATester);

    }
    /**
     * Fonction en charge de tester l'url et de renvoyer les informations obtenues
     *
     * @param String $url
     * @return array|ArrayCollection|null
     */
    public function getInformation(string $url)
    {
        $apple = "https://apps.apple.com";
        $googleplay = "https://play.google.com";

        //Cas application type apple
        if (is_int(strpos($url, $apple))) {

            return $this->getAppleAppData($url);
            //Cas application type android
        } else if (is_int(strpos($url, $googleplay))) {
            return $this->getAndroidData($url);
        } else {
            //Si type inconnu
            return null;
        }
    }

    /**
     * Fonction en charge de récuperer les informations relative a l'application android.
     *
     * @param String $urlAndroid
     * @return array|null
     */
    private function getAndroidData(string $urlAndroid)
    {
        try {

            $scraper = new Scraper();

            $app = $scraper->getApp($this->getAndroidID($urlAndroid), 'fr','fr');

            $lstDonnes = array(
                "app_nom" => $app['title'],
                "app_note" => $app['rating'],
                "app_nombreAvis" => $app['votes'],
                "app_os"=>'android'
            );
            return $lstDonnes;
        } catch (NotFoundException) {
            return null;
        }
    }

    /**
     * Fonction en charge de récuperer l'id de l'application android contenu dans l'URL
     *
     * @param String $urlAndroid
     * @return String $id de l'application
     */
    private function getAndroidID(string $urlAndroid)
    {
        // +1 pour ne pas inclure le =
        $positionDepart = strpos($urlAndroid, "=") + 1;
        $subString = substr("$urlAndroid", $positionDepart);
        $positionArrivee = strpos($subString, "&");

        // si on a un argument dans l'url
        if($positionArrivee){
            $tailleSequence = strlen($subString);
            $id = substr("$subString", 0, $positionArrivee - $tailleSequence);
        } else {
            $id = $subString;
        }

        return $id;
    }


    /**
     * Fonction en charge de récuperer les données d'intêret provenant de l'URL
     * @param String $urlApple
     * @return ArrayCollection
     */
    private function getAppleAppData(string $urlApple)
    {
        try {
            //selecteur CSS à cibler
            $classCssNoteAppleStore = ".we-customer-ratings__averages__display";
            $classCssTitreAppleSore = ".product-header__title";
            $classCssNbAvis = ".we-customer-ratings__count";

            $client = new Client();
            $crawler = $client->request('GET', $urlApple);

            //Recuperation du contenu des balises
            $appNom = $crawler->filter($classCssTitreAppleSore)->innerText();
            $appNote = $crawler->filter($classCssNoteAppleStore)->innerText();
            $appNombreAvis = $this->getNumberOfVote($crawler->filter($classCssNbAvis)->innerText());

            //
            $lstDonnes = array(
                "app_nom" => $appNom,
                "app_note" => $appNote,
                "app_nombreAvis" => $appNombreAvis,
                "app_os"=>"iOS"
            );
            return $lstDonnes;
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Fonction en charge de convertir le string en données de type float
     * @param String $nombreAvis
     * @return float|int
     */
    private function getNumberOfVote(string $nombreAvis)
    {

        //si la string contient une virgule, on le replace
        $app_nombreAvisAPoint = str_replace(',', '.', $nombreAvis);
        // si nombre avis contient un k pour mille
        $positionK = strpos($app_nombreAvisAPoint, "k");
        // si nombre avis contient un M pour millions
        $positionM = strpos($app_nombreAvisAPoint, "M");


        if (is_int($positionK)) {
            $app_nombreAvis = floatval($app_nombreAvisAPoint) * 1000;
        } else if (is_int($positionM)) {
            $app_nombreAvis = floatval($app_nombreAvisAPoint) * 1000000;
        } else {
            $app_nombreAvis = floatval($app_nombreAvisAPoint);
        }

        return $app_nombreAvis;

    }


}