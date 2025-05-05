<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EquipementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Equipement;
use App\Entity\Stock;
use App\Form\EquipType;
use App\Form\EditEquipType;
use App\Service\NotificationService;
use Psr\Log\LoggerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class EquipementsController extends AbstractController
{
    private NotificationService $notificationService;
    private HttpClientInterface $httpClient;
    private string $geminiApiKey;

    public function __construct(
        NotificationService $notificationService,
        HttpClientInterface $httpClient,
        string $geminiApiKeyy
    ) {
        $this->notificationService = $notificationService;
        $this->httpClient = $httpClient;
        $this->geminiApiKeyy = $geminiApiKeyy;
    }
    #[Route('/equipements', name: 'app_equipements')]
    public function index(EquipementRepository $equipementRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $equipements = $paginator->paginate(
            $equipementRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('equipements/index.html.twig', [
            'equipements' => $equipements,
        ]);
    }

    #[Route('/equipements/search', name: 'app_equipements_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $searchTerm = $request->query->get('search', '');
        
        $equipementRepository = $entityManager->getRepository(Equipement::class);
        $queryBuilder = $equipementRepository->createQueryBuilder('e')
            ->leftJoin('e.stock', 's');
        
        if (!empty($searchTerm)) {
            $queryBuilder
                ->where('e.nom LIKE :searchTerm OR e.marque LIKE :searchTerm OR e.reference LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }
        
        $equipements = $queryBuilder->getQuery()->getResult();

        $data = [];
        foreach ($equipements as $equipement) {
            $stock = $equipement->getStock();
            
            $data[] = [
                'id' => $equipement->getId(),
                'reference' => $equipement->getReference(),
                'nom' => $equipement->getNom(),
                'marque' => $equipement->getMarque(),
                'prix' => $stock ? $stock->getPrixvente() : 'N/A',
                'quantite' => $stock ? $stock->getQuantite() : 'N/A',
            ];
        }

        return new JsonResponse(['data' => $data]);
    }

    #[Route('/equipements/delete/{id}', name: 'app_equipements_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipement->getId(), $request->request->get('_token'))) {
            try {
                if ($equipement->getStock()) {
                    $entityManager->remove($equipement->getStock());
                }
                
                foreach ($equipement->getLignecommandes() as $lignecommande) {
                    $entityManager->remove($lignecommande);
                }
                
                foreach ($equipement->getPaniers() as $panier) {
                    $entityManager->remove($panier);
                }
                
                $entityManager->remove($equipement);
                $entityManager->flush();
                
                $this->addFlash('success', 'Équipement supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression: '.$e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        $page = $request->query->getInt('page', 1);
        
        return $this->redirectToRoute('app_equipements', ['page' => $page]);
    }

    #[Route('/equipements/edit/{id}', name: 'app_equipements_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $stock = $equipement->getStock();

        $form = $this->createForm(EditEquipType::class, $equipement, [
            'quantite' => $stock ? $stock->getQuantite() : 0,
            'prixvente' => $stock ? $stock->getPrixvente() : 0.0,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return $this->render('equipements/edit.html.twig', [
                    'form' => $form->createView(),
                    'equipement' => $equipement,
                ]);
            }

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/equipements';
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($uploadDirectory, $newFilename);

                $equipement->setImage('/uploads/equipements/' . $newFilename);
            }

            if (!$stock) {
                $stock = new Stock();
                $stock->setEquipement($equipement);
            }
            $stock->setQuantite($form->get('quantite')->getData());
            if ($stock->getQuantite() <= 0) {
                $this->notificationService->notifyStockDepleted(
                    $equipement,
                    $entityManager
                );
            }
            $stock->setPrixvente($form->get('prixvente')->getData());

            $entityManager->persist($equipement);
            $entityManager->persist($stock);
            $entityManager->flush();

            $this->addFlash('success', 'Équipement mis à jour avec succès');
            
            $page = $request->query->getInt('page', 1);
            
            return $this->redirectToRoute('app_equipements', ['page' => $page]);
        }

        return $this->render('equipements/edit.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }

    #[Route('/equipements/create', name: 'app_equipements_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/equipements';
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($uploadDirectory, $newFilename);
                $equipement->setImage('/uploads/equipements/' . $newFilename);
            } else {
                $equipement->setImage('/uploads/equipements/default.jpg');
            }

            $entityManager->persist($equipement);
            $entityManager->flush();

            $stock = new Stock();
            $stock->setEquipement($equipement);
            $stock->setQuantite($form->get('quantite')->getData());
            $stock->setPrixvente($form->get('prixvente')->getData());
            $entityManager->persist($stock);
            $entityManager->flush();

            $limitPerPage = 5;
            $totalEquipements = $entityManager->getRepository(Equipement::class)->count([]);
            $lastPage = ceil($totalEquipements / $limitPerPage);

            $this->addFlash('success', 'Équipement créé avec succès');
            
            return $this->redirectToRoute('app_equipements', ['page' => $lastPage]);
        }

        return $this->render('equipements/add_equipement.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/generate-description', name: 'app_generate_description', methods: ['POST'])]
    public function generateDescription(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? '';
        $marque = $data['marque'] ?? '';

        if (empty($nom) || empty($marque)) {
            return new JsonResponse(['error' => 'Nom et Marque sont requis'], 400);
        }

        try {
            $prompt = "Generate a short description (1-2 sentences) for a car equipment with name '$nom' and brand '$marque'.";
            $response = $this->httpClient->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => $this->geminiApiKeyy,
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 50,
                    ],
                ],
            ]);

            $result = $response->toArray();
            $description = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Description non disponible.';

            return new JsonResponse(['description' => $description]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la génération: ' . $e->getMessage()], 500);
        }
    }
}