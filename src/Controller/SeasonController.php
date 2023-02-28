<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Doctrine\ORM\Tools\DebugUnitOfWorkListener;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/season', name: 'season_')]
class SeasonController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(Request $request, SeasonRepository $seasonRepository): Response
    {
        $season = new Season();
        $seasonForm = $this->createForm(SeasonType::class, $season);

        $seasonForm->handleRequest($request);

        if ($seasonForm->isSubmitted() && $seasonForm->isValid()){
            //sauvegarder
            $seasonRepository->save($season, true);
            //ajout flash
            $this->addFlash("success", "Season added !");
            //redirection
            return $this->redirectToRoute('serie_show', ['id' => $season->getSerie()->getId()]);

        }


        return $this->render('season/add.html.twig', [
            'seasonForm' => $seasonForm->createView()
        ]);
    }
}
