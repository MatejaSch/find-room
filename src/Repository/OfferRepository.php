<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 *
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    public function add(Offer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Offer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds offers that have rooms attached and thumbnail image set
     * @return Offer[] Returns an array of Offer objects
     */
    public function findAvailableOffers($capacity = null): array
    {
         $queryBuilder = $this->createQueryBuilder('o')
            ->andWhere('o.thumbnail IS NOT NULL');

        if ($capacity !== null) {
            $queryBuilder->andWhere("o.capacity = :capacity")
                ->setParameter("capacity", $capacity);
        }
         return $queryBuilder->join('o.rooms', 'r')
                ->getQuery()
                ->getResult()
        ;
    }


    public function findOfferByID($id): ?Offer
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.thumbnail IS NOT NULL')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->join('o.rooms', 'r')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

//    public function findOneBySomeField($value): ?Offer
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
