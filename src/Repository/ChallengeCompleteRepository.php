<?php

namespace App\Repository;

use App\Entity\ChallengeComplete;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChallengeComplete>
 *
 * @method ChallengeComplete|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChallengeComplete|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChallengeComplete[]    findAll()
 * @method ChallengeComplete[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChallengeCompleteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChallengeComplete::class);
    }

//    /**
//     * @return ChallengeComplete[] Returns an array of ChallengeComplete objects
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

//    public function findOneBySomeField($value): ?ChallengeComplete
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
