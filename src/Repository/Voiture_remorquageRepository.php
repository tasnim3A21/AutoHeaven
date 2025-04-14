<?php

namespace App\Repository;

use App\Entity\Voiture_remorquage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Voiture_remorquageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture_remorquage::class);
    }

    // Add custom methods as needed
}