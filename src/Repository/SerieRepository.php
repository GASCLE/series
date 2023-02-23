<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 *
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function save(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBestSeries(){
        //en DQL
        //récupération des series avec un vote > 8 et une popularité > 100
        //ordonné par popularité

        /*$dql = "SELECT s FROM App\Entity\Serie as s
                WHERE s.vote > 8
                AND s.popularity > 100
                ORDER BY s.popularity DESC";*/

        //transforme le string en objet de requete
        /*$query = $this->getEntityManager()->createQuery($dql);*/


        //en DQL
        $qb = $this->createQueryBuilder('s');
        $qb
            ->addOrderBy('s.popularity', 'DESC')
            ->andWhere('s.vote > 8')
            ->andWhere('s.popularity > 100')
            ->setMaxResults(50);

        $query = $qb->getQuery();

        //ajoute une limite de résultat
        $query->setMaxResults(50);

        return $query->getResult();





    }
}
