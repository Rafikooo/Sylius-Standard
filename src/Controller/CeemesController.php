<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CeemesController extends AbstractController
{
    #[Route('/ceemes', name: 'app_ceemes')]
    public function index(): Response
    {
        return $this->render('ceemes/index.html.twig', [
            'controller_name' => 'CeemesController',
        ]);
    }
}
