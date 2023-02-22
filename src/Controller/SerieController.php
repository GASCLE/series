<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//annotation de la class qui permet de mutualiser les informations
#[Route('/serie', name:'serie_')]
class SerieController extends AbstractController
{




    #[Route('/serie/list', name: 'list')]
    public function list(SerieRepository $serieRepository): Response
    {
        //TODO recupérer la liste des series en BDD
        //$series = instance de SerieRepository passé en attribut de la fonction
        //j'utilise la fonction findall() de cette instance
        //$series = $serieRepository->findAll();

        //j'utilise la fonction findby() qui prend en parametre un premiertableau de clé valeurs
        //un second optionel d'order by. + limit de offset
        //$series = $serieRepository->findBy(["status"=>"ended"], ["popularity" =>'DESC'], 10, 10);

        //exemple les 50 series les mieux notés
        $series = $serieRepository->findBy([],["vote"=>"DESC"],50);

        //je donne en parametre la vue (fichier twig) que je renvois et/+
        //le tableau associatif qui renvois des données a la vue.
        //(nom de variable twig 'serie' / variable php $serie
        return $this->render('serie/list.html.twig',[
            'series' => $series
        ]);
    }





    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])] //id doit correspondre a un entier (\d = un chiffre. \d+=un nombre d'autant de chiifre possible
    public function show(int $id, SerieRepository $serieRepository): Response
    {
        //TODO récupération des infos de la serie
        $serie = $serieRepository->find($id);

        if(!$serie){
            throw $this->createNotFoundException("Oops ! Serie not found !");
        }


        return $this->render('serie/show.html.twig', [
            'serie' => $serie
        ]);
    }




    #[Route('/add', name: 'add')]
    //je donne en parametre a ma fonction "entityManagerInterface pour pouvoir m'en servir a l'interieur
    public function add(SerieRepository $serieRepository, EntityManagerInterface $entityManager): Response
    {
        $serie = new Serie();

        $serie
            ->setName("The Office")
            ->setBackdrop("backdrop.png")
            ->setDateCreated(new \DateTime())
            ->setGenres("comedy")
            ->setFirstAirDate(new \DateTime('2022-02-02'))
            ->setLastAirDate(new \DateTime("-6 month"))
            ->setPopularity(850.52)
            ->setPoster("poster.png")
            ->setTmdbId("123456")
            ->setVote(8.5)
            ->setStatus("Ended");

        $serie2 = new Serie();

        $serie2
            ->setName("Bureau des legendes")
            ->setBackdrop("backdrop.png")
            ->setDateCreated(new \DateTime())
            ->setGenres("drame")
            ->setFirstAirDate(new \DateTime('2022-02-02'))
            ->setLastAirDate(new \DateTime("-6 month"))
            ->setPopularity(850.52)
            ->setPoster("poster.png")
            ->setTmdbId("123456")
            ->setVote(8.5)
            ->setStatus("Ended");

        $entityManager->persist($serie);
        $entityManager->persist($serie2);
        $entityManager->flush();

        $serieRepository->remove($serie);
        $serieRepository->remove($serie2);

       /*
        //enregistrement en BDD
        $serieRepository->save($serie, true);//instance d'objet + boolean 'flush'
        //pour executer "true" la requete ou la sauvegarder "false" pour l'exectuer plus tard

        $serie->setName("The last of us");
        $serieRepository->save($serie, true);*/


        //TODO creer un forumlaire d'ajout de serie
        return $this->render('serie/add.html.twig',);
    }
}
