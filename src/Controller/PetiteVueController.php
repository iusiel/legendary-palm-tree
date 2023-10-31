<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PetiteVueController extends AbstractController
{
    #[Route('/petite/vue', name: 'app_petite_vue')]
    public function index(): Response
    {
        return $this->render('petite_vue/index.html.twig', [
            'controller_name' => 'PetiteVueController',
        ]);
    }
}
