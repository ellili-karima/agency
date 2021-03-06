<?php

namespace App\Repository;

use App\Entity\Bien;
use App\Entity\User;
use App\Data\SearchData;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Bien>
 *
 * @method Bien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bien[]    findAll()
 * @method Bien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry )
    {
        parent::__construct($registry, Bien::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Bien $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Bien $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

     /**
     * Undocumented function
     *
     * @return Bien[]
     */
    public function getLast5(): array
    {
        // createQueryBuilder créé la requête sur la table sélectionnée par l'alias (b pour bien)
        return $this->createQueryBuilder('b')
                
                // orderBy ajoute le ORDER BY à la requête
                ->orderBy('b.id', 'DESC')
                // setMaxResults ajoute la LIMIT
                ->setMaxResults(5)
                ->getQuery()
                ->getResult()
                ;
    }


    
    /**
     * recupere les biens en lien avec un fitre
     *
     * @param SearchData $search
     * @return void
     */
    public function findSearch(SearchData $search)
    {
        
        $qb = $this->createQueryBuilder('b');
        if(!empty($search->minpiece)){
            $qb = $qb
                ->andWhere('b.nbrepieces >= :minpiece')
                ->setParameter('minpiece', $search->minpiece);

        }
        if(!empty($search->maxpiece)){
            $qb = $qb
                ->andWhere('b.nbrepieces <= :maxpiece')
                ->setParameter('maxpiece', $search->maxpiece);

        }
        if(!empty($search->minsurface)){
            $qb = $qb
                ->andWhere('b.surface >= :minsurface')
                ->setParameter('minsurface', $search->minsurface);

        }
        if(!empty($search->maxsurface)){
            $qb = $qb
                ->andWhere('b.surface <= :maxsurface')
                ->setParameter('maxsurface', $search->maxsurface);

        }
        if(!empty($search->minprix)){
            $qb = $qb
                ->andWhere('b.prix >= :minprix')
                ->setParameter('minprix', $search->minprix);

        }
        if(!empty($search->maxprix)){
            $qb = $qb
                ->andWhere('b.prix <= :maxprix')
                ->setParameter('maxprix', $search->maxprix);

        }

        return $qb->getQuery()->getResult();
         
    }


    /**
     * return la pagination des pages
     *
     * @param [type] $page
     * @param [type] $limit
     * @return void
     */
    public function getPaginatedBiens($page, $limit)
    {
        $qb = $this->createQueryBuilder('b')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ;
        return $qb->getQuery()->getResult();
    }

    /**
     * Return le nombre des bien
     *
     * @return void
     */
    public function getTotalBiens()
    {
        // $a="A louer";
            $qb = $this->createQueryBuilder('b')
                ->select('COUNT(b)')
                ;
                return $qb->getQuery()->getSingleScalarResult();
           
    }

    /**
     * Return le nombre des biens à louer
     *
     * @return void
     */
    public function getBiensAlouer()
    {
        $a="A louer";
            $qb = $this->createQueryBuilder('b')
                ->select('COUNT(b)')
                ->andWhere('b.transactiontype = :val')
                ->setParameter('val', $a)
                ;
                return $qb->getQuery()->getSingleScalarResult();
           
    }

      /**
     * Return le nombre des biens à vendre
     *
     * @return void
     */
    public function getBiensAvendre()
    {
        $a="A vendre";
            $qb = $this->createQueryBuilder('b')
                ->select('COUNT(b)')
                ->andWhere('b.transactiontype = :val')
                ->setParameter('val', $a)
                ;
                return $qb->getQuery()->getSingleScalarResult();
           
    }

    /**
     * fonction pour faire le tri par nombre des pieces des biens
     *
     * @return void
     */
    public function triPieces(string $triPieces){
        $qb = $this->createQueryBuilder('b');
        

        if($triPieces == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.nbrepieces', $triPieces) ;
            $triPieces = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.nbrepieces', $triPieces) ;
            $triPieces = 'DESC';
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * fonction pour faire un tri par la surface des biens
     *
     * @return void
     */
    public function triSurfaces(string $triSurfaces){
        $qb = $this->createQueryBuilder('b');
        

        if($triSurfaces == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.surface', $triSurfaces) ;
            $triSurfaces = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.surface', $triSurfaces) ;
            $triSurfaces = 'DESC';
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * fonction pour faire un tri par le prix des biens
     *
     * @return void
     */
    public function triPrix(string $triPrix){
        $qb = $this->createQueryBuilder('b');
        

        if($triPrix == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.prix', $triPrix) ;
            $tri = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.prix', $triPrix) ;
            $triPrix = 'DESC';
        }

        return $qb->getQuery()->getResult();

    }


    /**
     * function filtre les biens par type ou par type de transaction
     *@return array
     */
    public function getfiltre(string $recherche): array
    {
        $query = $this->createQueryBuilder('b');

        if ($recherche == 'Appartement') {
            $query->andWhere('b.type = :type')
                ->setParameter('type', $recherche);
        }
        if ($recherche == 'Maison') {
            $query->andWhere('b.type = :type')
                ->setParameter('type', $recherche);
        }
        if ($recherche == 'Location') {
            $query->andWhere('b.transactiontype = :type')
                ->setParameter('type', $recherche);
        }
        if ($recherche == 'Vente') {
            $query->andWhere('b.transactiontype = :type')
                ->setParameter('type', $recherche);
        }
        return $query->getQuery()->getResult();
    }



    /**
     * fonction retourn la liste des biens pas utilisateur
     *
     * @param User $user
     * @return array
     */
    public function getBiensUser(User $user): array
    {
        
        $query = $this->createQueryBuilder('b')
            ->andWhere('b.employeur = :val')
            ->setParameter('val', $user->getId())
            ;
        return $query->getQuery()->getResult();
    }


    /**
     * fonction permet de faire une recherche des mots dans les biens
     *
     * @param [type] $searchword
     * @return array|null
     */
    public function findWithSearchword ($searchword): ?array
    {
        $qb = $this->createQueryBuilder('b');
        //ici on passe l'expression qu'on veut chercher dans le table b pour bien
        $qb->where($qb->expr()->like('b.titre', $qb->expr()->literal('%'. $searchword . '%')))
            ->orWhere($qb->expr()->like('b.transactiontype', $qb->expr()->literal('%'. $searchword . '%')))
            ->orWhere($qb->expr()->like('b.description', $qb->expr()->literal('%'. $searchword .'%')))
        ;
        return $qb->getQuery()->getResult();
    }
  

    // /**
    //  * @return Bien[] Returns an array of Bien objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bien
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
