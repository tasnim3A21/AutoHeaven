<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EquipementRepository;
use App\Repository\PanierRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Panier;
use App\Entity\User;
use App\Entity\Stock;
use Symfony\Component\HttpFoundation\Request;

final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find(3);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $paniers = $entityManager->getRepository(Panier::class)->findBy(['id' => $user]);

        $subtotal = 0;
        foreach ($paniers as $panier) {
            $equipement = $panier->getId_e();
            $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
            if ($stock) {
                $subtotal += $stock->getPrixvente() * $panier->getQuantite();
            }
        }

        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'subtotal' => $subtotal,
            'user' => $user,
        ]);
    }

    #[Route('/panier/remove/{id}', name: 'app_remove_from_cart', methods: ['GET'])]
    public function removeFromCart(int $id, EntityManagerInterface $entityManager): Response
    {
        $panier = $entityManager->getRepository(Panier::class)->find($id);
        if (!$panier) {
            throw $this->createNotFoundException('Cart item not found');
        }

        // Optional: Check if the cart item belongs to the current user (user ID 3)
        $user = $entityManager->getRepository(User::class)->find(3);
        if ($panier->getId() !== $user) {
            throw $this->createAccessDeniedException('You are not allowed to remove this item.');
        }

        $entityManager->remove($panier);
        $entityManager->flush();

        return $this->redirectToRoute('app_panier');
    }
}