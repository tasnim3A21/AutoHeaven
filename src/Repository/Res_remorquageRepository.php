<?php

namespace App\Repository;

use App\Entity\Res_remorquage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Res_remorquageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Res_remorquage::class);
    }

    // Add custom methods as needed
}