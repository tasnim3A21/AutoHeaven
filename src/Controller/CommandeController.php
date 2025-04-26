<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use Knp\Component\Pager\PaginatorInterface;

final class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(
        CommandeRepository $commandeRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $filter = $request->query->get('filter', 'all');
       
        $sort = $request->query->get('sort', 'c.date_com');
        $direction = $request->query->get('direction', 'DESC');
    
        // Construction de la requête de base
        $queryBuilder = $commandeRepository->createQueryBuilder('c')
            ->leftJoin('c.id', 'u')
            ->addSelect('u');
    
        // Filtre par statut
        if ($filter !== 'all') {
            $queryBuilder->andWhere('c.status = :status')
                ->setParameter('status', $filter);
        }
    
      
    
        // Gestion du tri
        $validSortFields = ['c.date_com', 'u.nom']; // Liste des champs autorisés
        $sortField = in_array($sort, $validSortFields) ? $sort : 'c.date_com';
        $sortDirection = in_array(strtoupper($direction), ['ASC', 'DESC']) ? $direction : 'DESC';
        
        $queryBuilder->orderBy($sortField, $sortDirection);
    
        // Configuration de la pagination sans utiliser les options problématiques
        $commandes = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10,
            [
                'wrap-queries' => true,
                'sortFieldWhitelist' => $validSortFields // Liste blanche des champs de tri
            ]
        );
    
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
            'currentFilter' => $filter,
           
            'currentSort' => $sortField,
            'currentDirection' => $sortDirection
        ]);
    }
   

    #[Route('/commande/search', name: 'app_commande_search', methods: ['GET'])]
    public function search(CommandeRepository $commandeRepository, Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->query->get('q', '');
            $filter = $request->query->get('filter', 'all');
    
            $queryBuilder = $commandeRepository->createQueryBuilder('c')
                ->join('c.id', 'u')
                ->addSelect('u')
                ->where('u.nom LIKE :search OR u.prenom LIKE :search')
                ->setParameter('search', '%'.$searchTerm.'%');
    
            if ($filter !== 'all') {
                $queryBuilder->andWhere('c.status = :status')
                    ->setParameter('status', $filter);
            }
    
            $commandes = $queryBuilder
                ->orderBy('c.date_com', 'DESC')
                ->getQuery()
                ->getResult();
    
            $results = [];
            foreach ($commandes as $commande) {
                $user = $commande->getId();
                $results[] = [
                    'id' => $commande->getIdCom(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'tel' => $user->getTel(),
                    'date' => $commande->getDateCom()->format('d/m/Y H:i'),
                    'montant' => number_format($commande->getMontantTotal(), 2),
                    'status' => $commande->getStatus(),
                    'statusClass' => $commande->getStatus() == 'traitée' ? 'success' : 'warning'
                ];
            }
    
            return $this->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    #[Route('/commande/confirm/{id}', name: 'app_commande_confirm')]
    public function confirm(int $id, EntityManagerInterface $entityManager): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $commande->setStatus('traitée');
        $entityManager->flush();

        $this->addFlash('success', 'Commande #' . $commande->getId_com() . ' confirmée avec succès');
        return $this->redirectToRoute('app_commande');
    }
    #[Route('/commande/details/{id}', name: 'app_commande_details', methods: ['GET'])]
public function details(int $id, CommandeRepository $commandeRepository): Response
{
   
   $commande = $commandeRepository->createQueryBuilder('c')
        ->select('c', 'u', 'lc', 'e')
        ->join('c.id', 'u') 
        ->leftJoin('c.lignecommandes', 'lc') 
        ->leftJoin('lc.id_e', 'e') 
        ->where('c.id_com = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
   /* $commande =$request->query->get('aff', '');*/

    if (!$commande) {
        throw $this->createNotFoundException('Commande non trouvée');
    }

    return $this->render('commande/details.html.twig', [
        'commande' => $commande,
    ]);
}
}