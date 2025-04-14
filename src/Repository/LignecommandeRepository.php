<?php

namespace App\Repository;

use App\Entity\Lignecommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LignecommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignecommande::class);
    }


    public function getTopSoldProducts(int $limit = 5): array
    {
        $results = $this->createQueryBuilder('lc')
            ->select('e.nom as name, SUM(lc.quantite) as quantity')
            ->join('lc.id_e', 'e')
            ->groupBy('lc.id_e')
            ->orderBy('quantity', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    
        $data = [
            'names' => [],
            'quantities' => []
        ];
    
        foreach ($results as $result) {
            $data['names'][] = $result['name'];
            $data['quantities'][] = (int) $result['quantity'];
        }
    
        return $data;
    }

    // Add custom methods as needed
}