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


    
        // recupere les biens en lien avec un fitre
    /**
     * Undocumented function
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
     * return les biens
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
     * Return number of bien
     *
     * @return void
     */
    public function getTotalBiens()
    {
        // $a="A louer";
            $qb = $this->createQueryBuilder('b')
                ->select('COUNT(b)')
                // ->andWhere('b.transactiontype = :val')
                // ->setParameter('val', $a)
                ;
                return $qb->getQuery()->getSingleScalarResult();
           
    }

    /**
     * Return number of bien
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
     * Return number of bien
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
    public function triPieces(string $tri){
        $qb = $this->createQueryBuilder('b');
        

        if($tri == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.nbrepieces', $tri) ;
            $tri = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.nbrepieces', $tri) ;
            $tri = 'DESC';
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * fonction pour faire le tri par la surface des biens
     *
     * @return void
     */
    public function triSurfaces(string $tri){
        $qb = $this->createQueryBuilder('b');
        

        if($tri == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.surface', $tri) ;
            $tri = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.surface', $tri) ;
            $tri = 'DESC';
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * fonction pour faire le tri par la surface des biens
     *
     * @return void
     */
    public function triPri(string $tri){
        $qb = $this->createQueryBuilder('b');
        

        if($tri == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.prix', $tri) ;
            $tri = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.prix', $tri) ;
            $tri = 'DESC';
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * fonction pour faire le tri par la surface des biens
     *
     * @return void
     */
    public function triSurface(string $ordre){
        $qb = $this->createQueryBuilder('b');
        

        if($ordre == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.surface', $ordre) ;
            $ordre = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.surface', $ordre) ;
            $ordre = 'DESC';
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * fonction pour faire le tri par le prix des biens
     *
     * @return void
     */
    public function triPrix(string $prixordre){
        $qb = $this->createQueryBuilder('b');
        

        if($prixordre == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.prix', $prixordre) ;
            $prixordre = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.prix', $prixordre) ;
            $prixordre = 'DESC';
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


    public function getBiensUser(User $user): array
    {
        
        $query = $this->createQueryBuilder('b')
            ->andWhere('b.employeur = :val')
            ->setParameter('val', $user->getId())
            ;
        return $query->getQuery()->getResult();
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
