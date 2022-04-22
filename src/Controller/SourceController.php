<?php

namespace App\Controller;

use App\Repository\SourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SourceController extends AbstractController
{
    #[Route('/api/source/{id}', name: 'source_id', methods: ['GET'])]
    public function getID(SourceRepository  $sourceRepo, $id): Response
    {
        //{id}
        return $this->json( $sourceRepo->findBy(array('application'=>$id)),200, [],['groups'=>'source']);

    }
}
