<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Phpml\Regression\LeastSquares;
use Phpml\ModelManager;

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

    public function aff(string $searchTerm): array 
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'u', 'lc', 'e')
            ->join('c.id', 'u') 
            ->leftJoin('c.lignecommandes', 'lc') 
            ->leftJoin('lc.id_e', 'e') 
            ->where('c.id_com = :id')
            ->setParameter('id', $searchTerm)
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

    public function getSalesPredictionWithPHPML(): array
    {
        $currentYear = date('Y');
        $results = $this->createQueryBuilder('c')
            ->select("
                MONTH(c.date_com) as month, 
                SUM(c.montant_total) as total
            ")
            ->where("YEAR(c.date_com) = :year")
            ->setParameter('year', $currentYear)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();

        if (count($results) < 3) { // Au moins 3 mois de données nécessaires
            return [];
        }

        // Préparation des données
        $samples = [];
        $targets = [];
        
        foreach ($results as $row) {
            $samples[] = [$row['month']];
            $targets[] = $row['total'];
        }

        // Entraînement du modèle
        $regression = new LeastSquares();
        $regression->train($samples, $targets);

        // Sauvegarde du modèle (optionnel)
        $modelManager = new ModelManager();
        $modelManager->saveToFile($regression, '../public/uploads/sales_model.phpml');

        // Prédiction pour les 3 prochains mois
        $predictions = [];
        $lastMonth = max(array_column($results, 'month'));
        
        for ($i = 1; $i <= 3; $i++) {
            $nextMonth = $lastMonth + $i;
            // Gestion du dépassement d'année
            if ($nextMonth > 12) {
                $nextMonth -= 12;
            }
            $predictions[$nextMonth] = max(0, $regression->predict([[$nextMonth]])[0]);
        }

        return [
            'actual' => $results,
            'predictions' => $predictions,
            'model_accuracy' => $this->calculateModelAccuracy($regression, $samples, $targets)
        ];
    }

    private function calculateModelAccuracy($model, $samples, $targets): float
    {
        $correct = 0;
        $count = count($samples);
        
        for ($i = 0; $i < $count; $i++) {
            $predicted = $model->predict([$samples[$i]])[0]; // Extract the first element
            // Avoid division by zero
            if ($targets[$i] != 0 && abs($predicted - $targets[$i]) / $targets[$i] < 0.15) {
                $correct++;
            } elseif ($targets[$i] == 0 && abs($predicted - $targets[$i]) < 0.15) {
                // Handle case where target is 0 (use absolute difference)
                $correct++;
            }
        }
        
        return round(($correct / $count) * 100, 2);
    }
}