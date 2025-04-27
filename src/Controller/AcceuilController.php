<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;
use App\Repository\LignecommandeRepository;

final class AcceuilController extends AbstractController
{
    #[Route('/acceuil', name: 'app_acceuil')]
    public function index(CommandeRepository $commandeRepository, LignecommandeRepository $ligneCommandeRepo): Response
    {
        // Statistiques par statut
        $statusStats = $commandeRepository->getCountByStatus();
    
        // Statistiques mensuelles pour toute l'année en cours
        $monthlySales = $commandeRepository->getMonthlySales();
    
        // Produits les plus vendus
        $topProducts = $ligneCommandeRepo->getTopSoldProducts(5); // Top 5
    
        return $this->render('acceuil/index.html.twig', [
            'statusStats' => $statusStats,
            'monthlySales' => $monthlySales,
            'topProducts' => $topProducts,
            'controller_name' => 'AccueilController',
        ]);
    }
}