<?php

namespace App\Repository;

use App\Entity\Camion_remorquage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Camion_remorquageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Camion_remorquage::class);
    }

    // Add custom methods as needed
}