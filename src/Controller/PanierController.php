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
use App\Entity\LignecommandeRepository;
use App\Entity\StockRepository;
use App\Entity\CommandeRepository;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


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
    #[Route('/checkout', name: 'app_checkout')]
public function checkout(EntityManagerInterface $entityManager, StripeService $stripeService): Response
{
    $user = $entityManager->getRepository(User::class)->find(3);
    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }

    $paniers = $entityManager->getRepository(Panier::class)->findBy(['id' => $user]);
    if (empty($paniers)) {
        $this->addFlash('error', 'Your cart is empty');
        return $this->redirectToRoute('app_panier');
    }

    // Calcul du total
    $total = 0;
    $lineItems = [];
    foreach ($paniers as $panier) {
        $equipement = $panier->getId_e();
        $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
        if ($stock) {
            $amount = $stock->getPrixvente() * 100; // Stripe utilise les centimes
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

    // Création de la session Stripe
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
    $user = $entityManager->getRepository(User::class)->find(3);
    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }

    // Récupérer les articles du panier
    $paniers = $entityManager->getRepository(Panier::class)->findBy(['id' => $user]);
    
    // Créer une nouvelle commande
    $commande = new Commande();
    $commande->setId($user);
    $commande->setDate_com(new \DateTime());
    $commande->setStatus('en attente');
    
    // Calculer le montant total
    $total = 0;
    foreach ($paniers as $panier) {
        $equipement = $panier->getId_e();
        $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
        if ($stock) {
            $total += $stock->getPrixvente() * $panier->getQuantite();
        }
    }
    $commande->setMontant_total($total);
    
    $entityManager->persist($commande);
    
    // Créer les lignes de commande et vider le panier
    foreach ($paniers as $panier) {
        $equipement = $panier->getId_e();
        $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
        
        if ($stock) {
            // Créer la ligne de commande
            $ligneCommande = new Lignecommande();
            $ligneCommande->setIdc($commande);
            $ligneCommande->setId_e($equipement);
            $ligneCommande->setQuantite($panier->getQuantite());
            $ligneCommande->setPrix_unitaire($stock->getPrixvente());
            
            $entityManager->persist($ligneCommande);
            
            // Mettre à jour le stock
            $stock->setQuantite($stock->getQuantite() - $panier->getQuantite());
            
            // Supprimer l'article du panier
            $entityManager->remove($panier);
        }
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