<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    #[Route('/report/generate', name: 'app_report_generate')]
    public function generate(): Response
    {
        return $this->render('report/generate.html.twig', [
            'controller_name' => 'ReportController',
        ]);
    }
}
