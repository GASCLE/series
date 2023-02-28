<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

//annotation de la class qui permet de mutualiser les informations
#[Route('/serie', name:'serie_')]
class SerieController extends AbstractController
{




    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'], methods: "GET")]
    public function list(SerieRepository $serieRepository, int $page = 1): Response
    {
        //TODO recupérer la liste des series en BDD
        //$series = instance de SerieRepository passé en attribut de la fonction
        //j'utilise la fonction findall() de cette instance
        $series = $serieRepository->findAll();

        //j'utilise la fonction findby() qui prend en parametre un premiertableau de clé valeurs
        //un second optionel d'order by. + limit de offset
        /*$series = $serieRepository->findBy(["status"=>"ended"], ["popularity" =>'DESC'], 10, 10);*/

        //exemple les 50 series les mieux notés
        /*$series = $serieRepository->findBy([],["vote"=>"DESC"],50);*/

        //je donne en parametre la vue (fichier twig) que je renvois et/+
        //le tableau associatif qui renvois des données a la vue.
        //(nom de variable twig 'serie' / variable php $serie

        $nbSerieMax = $serieRepository->count([]);
        $maxPage = ceil($nbSerieMax / SerieRepository::SERIE_LIMIT);

        if ($page >= 1 && $page <= $maxPage){
            //utilisation de la methode créé findBestSeries()
            $series = $serieRepository->findBestSeries($page);
        }else{
            throw $this->createNotFoundException("Oops ! page not found !");
        }
        return $this->render('serie/list.html.twig',[
            //on envoi les données a la vue
            'series' => $series,
            'currentPage' => $page,
            'maxPage' => $maxPage,

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
    #[IsGranted(["ROLE_USER"])]// je donne l'acces a cette route pour les roles USER
    //je donne en parametre a ma fonction "entityManagerInterface pour pouvoir m'en servir a l'interieur
    public function add(SerieRepository $serieRepository, /*EntityManagerInterface $entityManager*/ Request $request): Response
    {



        $serie = new Serie();

        //creation d'une instance de form lié a une instance de serie
        $serieForm = $this->createForm(SerieType::class, $serie);

        //méthode qui extrait les elements du dormulaire de la requete
        $serieForm->handleRequest($request);



        if($serieForm->isSubmitted() && $serieForm->isValid()){

            //upload photo
            /* @var UploadedFiles $file */

            $file = $serieForm->get('poster')->getData();
            //creation d'un nouveau nom
            $newFileName = $serie->getName() . "-" . uniqid() . "." . $file->guessExtension();
            //copie du fichier dans le répértoire de sauvegarde en le renommant
            $file->move('img/posters/series', $newFileName);
            //set le nouveau nom de la serie
            $serie->setPoster($newFileName);


            //set date de cration
            /*$serie->setDateCreated(new \DateTime());*/

            //sauvegarde en BDD
            $serieRepository->save($serie, true);


            $this->addFlash("success", "Serie added !");

            //redirige vers la page de details de la serie
            return $this->redirectToRoute('serie_show', ['id' => $serie->getId()]);
        }


        /*$serie
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
        $serieRepository->remove($serie2);*/


        //enregistrement en BDD
        /*$serieRepository->save($serie, true);*///instance d'objet + boolean 'flush'
        //pour executer "true" la requete ou la sauvegarder "false" pour l'exectuer plus tard

        /*$serie->setName("The last of us");
        $serieRepository->save($serie, true);*/


        //TODO creer un forumlaire d'ajout de serie
        return $this->render('serie/add.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);
    }



    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id, SerieRepository $serieRepository){
        //récupération de la serie
        $serie = $serieRepository->find($id);

        if($serie) {
            //je la supprime ou exception
            $serieRepository->remove($serie);
            $this->addFlash("warning", "Serie deleted !");
        }else{
            throw $this->createNotFoundException("This serie can't be deleted !");
        }

        return $this->redirectToRoute('serie_list');
    }

}
