<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }



    public function search(string $searchTerm): array
{
    return $this->createQueryBuilder('c')
        ->join('c.id', 'u')
        ->addSelect('u')
        ->where('u.nom LIKE :search OR u.prenom LIKE :search OR u.tel LIKE :search OR c.id_com LIKE :search')
        ->setParameter('search', '%'.$searchTerm.'%')
        ->orderBy('c.date_com', 'DESC')
        ->getQuery()
        ->getResult();
}
public function aff(string $searchTerm): array {
return $this->createQueryBuilder('c')
->select('c', 'u', 'lc', 'e')
->join('c.id', 'u') 
->leftJoin('c.lignecommandes', 'lc') 
->leftJoin('lc.id_e', 'e') 
->where('c.id_com = :id')
->setParameter('id', $id)
->getQuery()
->getOneOrNullResult();

   } 
   public function getCountByStatus(): array
{
    $results = $this->createQueryBuilder('c')
        ->select('c.status, COUNT(c.id_com) as count')
        ->groupBy('c.status')
        ->getQuery()
        ->getResult();

    $data = [
        'labels' => [],
        'data' => []
    ];

    foreach ($results as $result) {
        $data['labels'][] = ucfirst($result['status']);
        $data['data'][] = $result['count'];
    }

    return $data;
}

public function getMonthlySales(): array
{
    $currentYear = date('Y');
    $currentMonth = date('n'); // Mois actuel (1-12)
    $startDate = new \DateTime("$currentYear-01-01");
    $endDate = new \DateTime("$currentYear-12-31");

    $results = $this->createQueryBuilder('c')
        ->select("
            DATE_FORMAT(c.date_com, '%Y-%m') as month, 
            SUM(c.montant_total) as total
        ")
        ->where('c.date_com BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->groupBy('month')
        ->orderBy('month', 'ASC')
        ->getQuery()
        ->getResult();

    // Générer tous les mois de l'année
    $period = new \DatePeriod(
        $startDate,
        new \DateInterval('P1M'),
        $endDate
    );

    $data = ['months' => [], 'amounts' => []];
    $monthCounter = 1;
    
    foreach ($period as $date) {
        $monthKey = $date->format('Y-m');
        $monthLabel = $date->format('M');
        $data['months'][] = $monthLabel;
        
        // Si le mois est dans le futur, on met null pour ne pas afficher de point
        if ($monthCounter > $currentMonth) {
            $data['amounts'][] = null;
        } else {
            $total = 0;
            foreach ($results as $row) {
                if ($row['month'] === $monthKey) {
                    $total = (float) $row['total'];
                    break;
                }
            }
            $data['amounts'][] = $total;
        }
        
        $monthCounter++;
    }

    return $data;
}

}