<?php

namespace App\Repository;

use App\Entity\ficheProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ficheProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method ficheProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method ficheProduit[]    findAll()
 * @method ficheProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ficheProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ficheProduit::class);
    }

    // /**
    //  * @return ficheProduit[] Returns an array of ficheProduit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ficheProduit
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
