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
}
