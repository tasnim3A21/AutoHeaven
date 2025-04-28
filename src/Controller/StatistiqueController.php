<?php
namespace App\Controller;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueController extends AbstractController
{
    #[Route('/statistique', name: 'app_statistique')]
    public function index(AvisRepository $avisRepository): Response
    {
        // Récupérer les statistiques par voiture
        $stats = $avisRepository->getStatsParNote();


        // Retourner la vue avec les statistiques
        return $this->render('statistique/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}
