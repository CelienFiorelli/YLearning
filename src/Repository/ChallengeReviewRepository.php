<?php

namespace App\Repository;

use App\Entity\ChallengeReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChallengeReview>
 *
 * @method ChallengeReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChallengeReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChallengeReview[]    findAll()
 * @method ChallengeReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChallengeReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChallengeReview::class);
    }

//    /**
//     * @return ChallengeReview[] Returns an array of ChallengeReview objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ChallengeReview
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
