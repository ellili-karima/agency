<?php

namespace App\Repository;

use App\Entity\Bien;
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
        $qb = $this->createQueryBuilder('b')
            ->select('COUNT(b)')
            ;
            return $qb->getQuery()->getSingleScalarResult();
    }


    public function triBiens(){
        $qb = $this->createQueryBuilder('b');
        $tri = 'ASC';

        if($tri)
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
