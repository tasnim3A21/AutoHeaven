<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Equipement;
use App\Entity\Stock;
use App\Entity\Panier;
use App\Entity\User;
use App\Repository\EquipementRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;

final class EquipementController extends AbstractController
{
    #[Route('/equipement', name: 'app_equipement')]
    public function index(EquipementRepository $equipementRepository, EntityManagerInterface $entityManager): Response
    {
        $equipements = $entityManager->getRepository(Equipement::class)->findAll();
        $stocks = $entityManager->getRepository(Stock::class)->findAll();

        return $this->render('equipement/index.html.twig', [
            'equipements' => $equipements,
            'stocks' => $stocks,
        ]);
    }

    #[Route('/equipement/{id}', name: 'app_equipement_detail', methods: ['GET'])]
    public function detail(int $id, EntityManagerInterface $entityManager): Response
    {
        $equipement = $entityManager->getRepository(Equipement::class)->find($id);

        if (!$equipement) {
            throw $this->createNotFoundException('The equipment does not exist');
        }

        $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);

        return $this->render('equipement/detail.html.twig', [
            'equipement' => $equipement,
            'stock' => $stock,
        ]);
    }

    #[Route('/equipement/add-to-cart/{id}', name: 'app_add_to_cart', methods: ['POST'])]
    public function addToCart(int $id, Request $request, EntityManagerInterface $entityManager, PanierRepository $panierRepository): Response
    {
        $equipement = $entityManager->getRepository(Equipement::class)->find($id);
        if (!$equipement) {
            return new JsonResponse([
                'success' => false,
                'message' => 'The equipment does not exist.'
            ], 404);
        }

        $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
        if (!$stock) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Stock not found for this equipment.'
            ], 404);
        }

        $quantity = (int) $request->request->get('quantity', 1);

        if ($quantity > $stock->getQuantite()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Requested quantity exceeds available stock. Only ' . $stock->getQuantite() . ' items are available.'
            ]);
        }

        $user = $entityManager->getRepository(User::class)->find(3);
        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $panier = $panierRepository->findOneBy([
            'id' => $user,
            'id_e' => $equipement,
        ]);

        if ($panier) {
            $newQuantity = $panier->getQuantite() + $quantity;
            if ($newQuantity > $stock->getQuantite()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Total quantity in cart exceeds available stock. Only ' . $stock->getQuantite() . ' items are available.'
                ]);
            }
            $panier->setQuantite($newQuantity);
        } else {
            $panier = new Panier();
            $panier->setId($user);
            $panier->setId_e($equipement);
            $panier->setQuantite($quantity);
            $entityManager->persist($panier);
        }

        $entityManager->flush();
 
        return new JsonResponse([
            'success' => true,
            'message' => 'Item added to cart successfully.'
        ]);
    }
}