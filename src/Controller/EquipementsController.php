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
use Psr\Log\LoggerInterface;
use  Knp\Component\Pager\PaginatorInterface;

final class EquipementsController extends AbstractController
{
    #[Route('/equipements', name: 'app_equipements')]
    public function index(EquipementRepository $equipementRepository, EntityManagerInterface $entityManager , PaginatorInterface $paginator ,Request $request): Response
    {
       $equipements = $paginator->paginate(
            $equipementRepository->findAll(), // query NOT result
            $request->query->getInt('page',1), // page number (default is 1)
            5// limit per page (default is 10)
        );
       
        // $equipements = $entityManager->getRepository(Equipement::class)->findAll();
        
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
            
           /* // Supprimer les autres entités liées
            foreach ($equipement->getOffres() as $offre) {
                $entityManager->remove($offre);
            }*/
    
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
    /*#[Route('/equipements/update/{id}', name: 'app_equipements_update', methods: ['POST'])]
    public function update(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Équipement mis à jour avec succès');
            return $this->redirectToRoute('app_equipements');
        }

        return $this->render('equipements/edit.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }*/
   /* ee #[Route('/equipements/edit/{id}', name: 'app_equipements_edit', methods: ['GET'])]
    public function editForm(Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
        
        $form = $this->createForm(EditEquipType::class, $equipement, [
            'quantite' => $stock ? $stock->getQuantite() : 0,
            'prixvente' => $stock ? $stock->getPrixvente() : 0.0,
        ]);
    
        return $this->render('equipements/edit.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }*/

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

        // Traitement de l'image
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
   /* #[Route('/equipements/update/{id}', name: 'app_equipements_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
        
        $form = $this->createForm(EditEquipType::class, $equipement, [
            'quantite' => $stock ? $stock->getQuantite() : 0,
            'prixvente' => $stock ? $stock->getPrixvente() : 0.0,
        ]);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/equipements';
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($uploadDirectory, $newFilename);
                
                $oldImage = $equipement->getImage();
                if ($oldImage && $oldImage !== '/uploads/equipements/default.jpg') {
                    $oldImagePath = $this->getParameter('kernel.project_dir') . '/public' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                $equipement->setImage('/uploads/equipements/' . $newFilename);
            }
    
            if (!$stock) {
                $stock = new Stock();
                $stock->setId($equipement);
            }
            $stock->setQuantite($form->get('quantite')->getData());
            $stock->setPrixvente($form->get('prixvente')->getData());
            
            $entityManager->persist($equipement);
            $entityManager->persist($stock);
            $entityManager->flush();
    
            $this->addFlash('success', 'Équipement mis à jour avec succès');
            return $this->redirectToRoute('app_equipements');
        }
    
        return $this->render('equipements/edit.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }*/
   
 /*u   #[Route('/equipements/update/{id}', name: 'app_equipements_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
        
        $form = $this->createForm(EditEquipType::class, $equipement, [
            'quantite' => $stock ? $stock->getQuantite() : 0,
            'prixvente' => $stock ? $stock->getPrixvente() : 0.0,
        ]);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/equipements';
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($uploadDirectory, $newFilename);
                
                $oldImage = $equipement->getImage();
                if ($oldImage && $oldImage !== '/uploads/equipements/default.jpg') {
                    $oldImagePath = $this->getParameter('kernel.project_dir') . '/public' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                $equipement->setImage('/uploads/equipements/' . $newFilename);
            }
    
            if (!$stock) {
                $stock = new Stock();
                $stock->setId($equipement);
            }
            $stock->setQuantite($form->get('quantite')->getData());
            $stock->setPrixvente($form->get('prixvente')->getData());
            
            $entityManager->persist($equipement);
            $entityManager->persist($stock);
            $entityManager->flush();
    
            $this->addFlash('success', 'Équipement mis à jour avec succès');
            return $this->redirectToRoute('app_equipements');
        }
    
        return $this->render('equipements/edit.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }*/
/*public function update(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
{
    $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
    
    $form = $this->createForm(EquipType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'image
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/equipements';
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($uploadDirectory, $newFilename);
            
            // Supprimer l'ancienne image si ce n'est pas l'image par défaut
            $oldImage = $equipement->getImage();
            if ($oldImage && $oldImage !== '/uploads/equipements/default.jpg') {
                $oldImagePath = $this->getParameter('kernel.project_dir') . '/public' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $equipement->setImage('/uploads/equipements/' . $newFilename);
        }

        // Mise à jour du stock
        if (!$stock) {
            $stock = new Stock();
            $stock->setId($equipement);
        }
        $stock->setQuantite($form->get('quantite')->getData());
        $stock->setPrixvente($form->get('prixvente')->getData());
        
        $entityManager->persist($stock);
        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Équipement mis à jour avec succès'
            ]);
        }

        $this->addFlash('success', 'Équipement mis à jour avec succès');
        return $this->redirectToRoute('app_equipements');
    }

    return $this->render('equipements/edit.html.twig', [
        'form' => $form->createView(),
        'equipement' => $equipement,
    ]);}*/



    
    #[Route('/equipements/create', name: 'app_equipements_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
     {
        $equipement = new Equipement();
        $form = $this->createForm(EquipType::class, $equipement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
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
}