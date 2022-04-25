<?php

namespace App\Service;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Goutte\Client;
use InvalidArgumentException;
use Raulr\GooglePlayScraper\Exception\NotFoundException;
use Raulr\GooglePlayScraper\Scraper;

class Scrapping
{

    private ApplicationRepository $applicationRepository;

    public function __construct( ApplicationRepository $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
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
            $app = $scraper->getApp($this->getAndroidID($urlAndroid));

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
        $tailleSequence = strlen($subString);
        $id = substr("$subString", 0, $positionArrivee - $tailleSequence);
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