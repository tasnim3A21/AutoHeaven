<?php

namespace App\Repository;

use App\Entity\Service_remorquage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Service_remorquageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service_remorquage::class);
    }

    // Add custom methods as needed
}