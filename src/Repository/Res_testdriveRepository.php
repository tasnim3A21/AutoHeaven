<?php

namespace App\Repository;

use App\Entity\Res_testdrive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Res_testdriveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Res_testdrive::class);
    }

    // Add custom methods as needed
}