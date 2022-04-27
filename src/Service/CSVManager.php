<?php

namespace App\Service;

use App\Repository\DonnesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CSVManager
{

    private DonnesRepository $donnesRepository;

    public function __construct(DonnesRepository $donnesRepository)
    {
        $this->donnesRepository = $donnesRepository;
    }

    public function generateCsvAction(){

        return $this->donnesRepository->getAllByID();





}

}