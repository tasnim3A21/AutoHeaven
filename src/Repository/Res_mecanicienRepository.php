<?php

namespace App\Repository;

use App\Entity\Res_mecanicien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Res_mecanicienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Res_mecanicien::class);
    }

    // Add custom methods as needed
}