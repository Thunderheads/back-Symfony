<?php

namespace App\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Goutte\Client;
use Raulr\GooglePlayScraper\Scraper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrangeGnomeController extends AbstractController
{
    #[Route('/orange', name: 'app_orange_gnome')]
    public function index(): Response
    {


        // Android
        $idGoogleStoreMarche = "com.kiloo.subwaysurf";
        $idGoogleStore = "com.orange.caraibe.orangeetmoicaraibe";



        $client = new Client();
        $crawler = $client->request('GET', 'https://play.google.com/store/apps/details?id=fr.harmonie.mutuelle.mobile&hl=fr');

        $classCssTitreHuaweiAppGallery = ".title";
        $classCssNoteHuaweiAppGallery = ".count";
        $classCssEvalutationsHuaweiAppGallery = ".commentators";


        dd($crawler->html());

        $scraper = new Scraper();
        // mentionner la langue parce que sinon ça renvoie 0
        //$app = $scraper->getApp($idGoogleStore, 'fr','fr');
        $app = $scraper->getApp($idGoogleStore, 'fr', 'fr');
        dd($app);


        // Apple
        $urlapple = "https://apps.apple.com/fr/app/numbers/id361304891";
        $classCssNoteAppleStore = ".we-customer-ratings__averages__display";
        $classCssTitreAppleSore = ".product-header__title";
        //pour voir le rendu html
        //$data = file_get_contents($urlandroid);
        $client = new Client();
        $crawler = $client->request('GET', $urlapple);
        //avec ça on récupere une note sur le store d'apple comment c'est du HTML avec goutte ça marche
        $crawler->filter($classCssTitreAppleSore)->innerText();


        return $this->render('orange_gnome/index.html.twig', [
            'controller_name' => 'OrangeGnomeController',
        ]);
    }
}
