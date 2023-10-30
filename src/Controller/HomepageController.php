<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route("/", name: "app_homepage")]
    public function index(): Response
    {
        $listings = [
            [
                "title" => "Petite Vue",
                "description" =>
                    "From what I understand, you can use petite vue if you do not want to turn your website into a full blown Vue application and just use some Vue features into your website.",
                "websiteLink" => "https://github.com/vuejs/petite-vue",
                "testPageLink" => "#",
            ],
        ];

        return $this->render("homepage/index.html.twig", [
            "listings" => $listings,
        ]);
    }
}
