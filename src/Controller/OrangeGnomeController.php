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
    #[Route('/', name: 'app_orange_gnome')]
    public function index(): Response
    {




        // Android
        $idGoogleStore = "com.supercell.clashroyale";

        $scraper = new Scraper();
        $app = $scraper->getApp($idGoogleStore);
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
        dd($crawler->filter($classCssTitreAppleSore)->innerText());


        return $this->render('orange_gnome/index.html.twig', [
            'controller_name' => 'OrangeGnomeController',
        ]);
    }
}
