<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/home', name: 'main_home')]
    public function home(): Response
    {
        $username = "Clément";
        $serie = ['title' => 'Community', 'year' => 'Ouf', 'platform' => 'NBC'];

        return $this->render("main/home.html.twig", [
            //la clé devient le nom de la variable coté twig
            "name" => $username,
            "serie" => $serie
        ]);
    }

    /**
     * @route("/test", name="main-test")
     */
    public function test(): Response
    {
        return $this->render('main/test.html.twig');
    }
    }

