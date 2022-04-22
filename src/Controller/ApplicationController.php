<?php

namespace App\Controller;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    #[Route('/api/application', name: 'application_all', methods: ['GET'])]
    public function getAll(ApplicationRepository $applicationRepo): Response
    {
        //pour recuperer les dates du jour dd((new \DateTime())->format('Y-m-d'));
        $applications = $applicationRepo->findAll();
        foreach ($applications as $application){
            foreach ($application->getDatas() as $key => $data) {
                if ($data->getDateCollect() == new \DateTime('2022-03-18') || $data->getDateCollect() == new \DateTime('2022-03-17')) {
                } else {
                    $application->getDatas()->remove($key);
                }
            }
    }
        return $this->json($applicationRepo->test(),200, [],['groups'=>'application']);
    }

    #[Route('/api/application/{id}', name: 'application_id', methods: ['GET'])]
    public function getID(Application $application): Response
    {
        return $this->json($application,200, [],['groups'=>'application']);

    }
}
