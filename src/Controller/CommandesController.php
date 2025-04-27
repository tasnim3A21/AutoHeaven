<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;

final class CommandesController extends AbstractController
{
    #[Route('/commandes', name: 'app_commandes')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les commandes de l'utilisateur avec ID 3
        $commandes = $entityManager->getRepository(Commande::class)
            ->findBy(['id' => 3], ['date_com' => 'DESC']);

        return $this->render('commandes/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commandes/{id}', name: 'app_commandes_details')]
    public function details(Commande $commande): Response
    {
        return $this->render('commandes/det.html.twig', [
            'commande' => $commande,
        ]);
    }
}