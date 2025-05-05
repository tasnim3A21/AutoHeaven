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
use App\Entity\Commande;
use App\Entity\Lignecommande;
use App\Service\StripeService;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PanierController extends AbstractController
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    #[Route('/panier', name: 'app_panier')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        $paniers = $entityManager->getRepository(Panier::class)->findBy(['id' => $user]);
    
        $subtotal = 0;
        foreach ($paniers as $panier) {
            $equipement = $panier->getId_e();
            $stock = $equipement->getStock();
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

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($panier->getId() !== $user) {
            throw $this->createAccessDeniedException('You are not allowed to remove this item.');
        }

        $entityManager->remove($panier);
        $entityManager->flush();

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(EntityManagerInterface $entityManager, StripeService $stripeService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        $paniers = $entityManager->getRepository(Panier::class)->findBy(['id' => $user]);
        if (empty($paniers)) {
            $this->addFlash('error', 'Your cart is empty');
            return $this->redirectToRoute('app_panier');
        }
    
        $total = 0;
        $lineItems = [];
        foreach ($paniers as $panier) {
            $equipement = $panier->getId_e();
            $stock = $equipement->getStock();
            if ($stock) {
                $amount = $stock->getPrixvente() * 100;
                $total += $stock->getPrixvente() * $panier->getQuantite();
    
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $equipement->getNom(),
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => $panier->getQuantite(),
                ];
            }
        }
    
        $session = $stripeService->createPaymentSession(
            $this->generateUrl('app_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->generateUrl('app_panier', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $lineItems,
            ['user_id' => $user->getId()]
        );
    
        return $this->redirect($session->url, 303);
    }

    #[Route('/payment/success', name: 'app_payment_success')]
    public function paymentSuccess(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        $paniers = $entityManager->getRepository(Panier::class)->findBy(['id' => $user]);
        if (empty($paniers)) {
            throw $this->createNotFoundException('Cart is empty');
        }

        // Créer la commande
        $commande = new Commande();
        $commande->setId($user);
        $commande->setDate_com(new \DateTime());
        $commande->setStatus('en attente');
        
        $total = 0;
        $equipementQuantities = [];

        // Regrouper les quantités par équipement
        foreach ($paniers as $panier) {
            $equipement = $panier->getId_e();
            $equipementId = $equipement->getId();
            $quantity = $panier->getQuantite();
            
            if (!isset($equipementQuantities[$equipementId])) {
                $equipementQuantities[$equipementId] = [
                    'equipement' => $equipement,
                    'quantity' => 0,
                    'stock' => $equipement->getStock(),
                    'prix_vente' => $equipement->getStock() ? $equipement->getStock()->getPrixvente() : 0,
                ];
            }
            $equipementQuantities[$equipementId]['quantity'] += $quantity;
            $total += $equipementQuantities[$equipementId]['prix_vente'] * $quantity;
        }

        $commande->setMontant_total($total);
        $entityManager->persist($commande);

        // Traiter chaque équipement une seule fois
        foreach ($equipementQuantities as $data) {
            $equipement = $data['equipement'];
            $stock = $data['stock'];
            $totalQuantity = $data['quantity'];
            
            if ($stock) {
                // Créer une ligne de commande
                $ligneCommande = new Lignecommande();
                $ligneCommande->setIdc($commande);
                $ligneCommande->setId_e($equipement);
                $ligneCommande->setQuantite($totalQuantity);
                $ligneCommande->setPrix_unitaire($stock->getPrixvente());
                
                $entityManager->persist($ligneCommande);
                
                // Mettre à jour le stock
                $oldQuantity = $stock->getQuantite();
                $newQuantity = $oldQuantity - $totalQuantity;
                $stock->setQuantite($newQuantity);
                
                // Vérifier si le stock passe de >0 à <=0
                if ($oldQuantity > 0 && $newQuantity <= 0) {
                    $this->notificationService->notifyStockDepleted($equipement, $entityManager);
                }
            }
        }

        // Supprimer les éléments du panier
        foreach ($paniers as $panier) {
            $entityManager->remove($panier);
        }

        $entityManager->flush();
        
        return $this->render('payment/success.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/panier/update-quantity/{id}', name: 'app_update_quantity', methods: ['POST'])]
    public function updateQuantity(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $panier = $entityManager->getRepository(Panier::class)->find($id);
        if (!$panier) {
            throw $this->createNotFoundException('Cart item not found');
        }

        $newQuantity = $request->request->get('quantity');
        if ($newQuantity < 1) {
            $newQuantity = 1;
        }

        $panier->setQuantite($newQuantity);
        $entityManager->flush();

        return $this->redirectToRoute('app_panier');
    }
}