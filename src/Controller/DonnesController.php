<?php

namespace App\Controller;

use App\Repository\DonnesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DonnesController extends AbstractController
{

    #[Route('/api/donnes', name: 'app_donnes')]
    public function index(DonnesRepository $donnesRepository): Response
    {

        $date = (new \DateTime('-1 year'))->format('Y-m-d');
        return $this->json($donnesRepository->findBy(array('dateCollect'=>new \DateTime($date))),200, [],['groups'=>'donnes']);

    }
}
