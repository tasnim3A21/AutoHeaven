<?php

namespace App\Repository;

use App\Entity\Mention_j_aime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Mention_j_aimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mention_j_aime::class);
    }

    // Add custom methods as needed
}