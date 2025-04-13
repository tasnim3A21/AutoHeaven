<?php
namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }
    // src/Repository/AvisRepository.php

    public function getStatsParVoiture()
    {
        return $this->createQueryBuilder('a')
            ->select('v.id_v AS id_v', 
                     'SUM(CASE WHEN a.note >= 4 THEN 1 ELSE 0 END) AS bons', 
                     'SUM(CASE WHEN a.note < 4 THEN 1 ELSE 0 END) AS mauvais')
            ->join('a.idV', 'v')  // Jointure avec l'entité Voiture en utilisant 'idV'
            ->groupBy('v.id_v')    // Grouper par l'ID de la voiture
            ->getQuery()
            ->getResult();
    }
    
    
}
