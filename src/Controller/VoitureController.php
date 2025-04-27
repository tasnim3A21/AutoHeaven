<?php

namespace App\Controller;

use App\Entity\Voiture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Form\VoitureType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class VoitureController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/voiture', name: 'app_voiture', methods: ['GET'])]
public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    $searchTerm = $request->query->get('search', ''); // Retrieve the search term

    // Create a query builder for the Voiture repository
    $queryBuilder = $entityManager->getRepository(Voiture::class)->createQueryBuilder('v');
    
    // If there is a search term, apply filters to the query
    if (!empty($searchTerm)) {
        $queryBuilder
            ->where('v.marque LIKE :searchTerm')
            ->orWhere('v.couleur LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%');
    }

    // Execute the query and get the result
    $voitures = $queryBuilder->getQuery()->getResult();

    return $this->render('voiture/index.html.twig', [
        'voitures' => $voitures,
        'search' => $searchTerm,  // Pass the search term to the view to populate the input field
    ]);
}

#[Route('/voiture/new', name: 'voiture_new')]
public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    $voiture = new Voiture();
    $form = $this->createForm(VoitureType::class, $voiture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData(); // Assuming 'imageFile' is the form field name

        if ($imageFile) {
            // Get the original filename and use it as is (without generating a new name)
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

            // Optionally, slugify the filename to ensure no illegal characters, if desired
            $safeFilename = $slugger->slug($originalFilename);

            // Keep the original extension
            $newFilename = $safeFilename . '.' . $imageFile->guessExtension();

            try {
                // Move the file to the directory where it should be stored
                $imageFile->move(
                    $this->getParameter('voiture_images_directory'), // Make sure this parameter points to your 'public/images' directory
                    $newFilename
                );

                // Set the image filename to store in the database (this is what will be saved)
                $voiture->setImage($newFilename);
            } catch (FileException $e) {
                // Handle exception, e.g. log error or show an error message
                $this->addFlash('error', 'There was an issue uploading the image.');
            }
        }

        // Save voiture to the database
        $em->persist($voiture);
        $em->flush();

        return $this->redirectToRoute('app_voiture');
    }

    return $this->render('voiture/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    

#[Route('/voiture/{id}/edit', name: 'voiture_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Voiture $voiture, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    $form = $this->createForm(VoitureType::class, $voiture);
    $form->handleRequest($request);

    // If the form is submitted and valid
    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            // Get the original filename and use it as is (without generating a new name)
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

            // Optionally, slugify the filename to ensure no illegal characters, if desired
            $safeFilename = $slugger->slug($originalFilename);

            // Keep the original extension
            $newFilename = $safeFilename . '.' . $imageFile->guessExtension();

            try {
                // Move the file to the directory where it should be stored
                $imageFile->move(
                    $this->getParameter('voiture_images_directory'), // Ensure this is pointing to your public/images directory
                    $newFilename
                );

                // Set the new image filename in the Voiture entity (overwrite the previous one)
                $voiture->setImage($newFilename);
            } catch (FileException $e) {
                // Handle exception if something goes wrong
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
            }
        }

        // Persist the changes to the database
        $em->flush();

        // Add a flash success message
        $this->addFlash('success', 'Voiture modifiée avec succès !');

        // Redirect to the voiture listing page
        return $this->redirectToRoute('app_voiture');
    }

    // Render the form if it's not submitted or valid
    return $this->render('voiture/edit.html.twig', [
        'form' => $form->createView(),
        'voiture' => $voiture,  // Optionally, you can pass the voiture to the template to show current details
    ]);
}


    #[Route('/voiture/{id}/delete', name: 'voiture_delete', methods: ['POST'])]
public function delete(Request $request, Voiture $voiture): Response
{
    // Check if the CSRF token is valid
    if ($this->isCsrfTokenValid('delete' . $voiture->getId_v(), $request->request->get('_token'))) {
        try {
            // Remove the voiture
            $this->entityManager->remove($voiture);
            $this->entityManager->flush();

            // Add flash message for success
            $this->addFlash('success', 'Voiture supprimée avec succès !');
        } catch (\Exception $e) {
            // Handle error (optional)
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression.');
        }
    } else {
        $this->addFlash('error', 'Le token CSRF est invalide.');
    }

    // Redirect back to the voiture list
    return $this->redirectToRoute('app_voiture');
}
    #[Route('/voiture/{id}/details', name: 'voiture_details', methods: ['GET'])]
    public function details(Voiture $voiture): Response
    {
        return $this->render('voiture/details.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/voiture/search', name: 'voiture_search', methods: ['GET'])]
public function search(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    // Retrieve the search term from the request
    $searchTerm = $request->query->get('search', '');

    // Get the repository for the Voiture entity
    $voitureRepository = $entityManager->getRepository(Voiture::class);
    
    // Create the query builder to build the search query
    $queryBuilder = $voitureRepository->createQueryBuilder('v')
        ->leftJoin('v.id_c', 'c') // Join with Categorie if necessary
        ->where('1 = 1'); // Default condition to allow chaining `orWhere` later

    // If a search term is provided, filter by marque, description, or couleur
    if (!empty($searchTerm)) {
        $queryBuilder
            ->andWhere('v.marque LIKE :searchTerm')
            ->orWhere('v.couleur LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%');
    }

    // Execute the query
    $voitures = $queryBuilder->getQuery()->getResult();

    // Prepare the data to return
    $data = [];
    foreach ($voitures as $voiture) {
        $data[] = [
            'id' => $voiture->getId_v(),
            'marque' => $voiture->getMarque(),
            'description' => $voiture->getDescription(),
            'kilometrage' => $voiture->getKilometrage(),
            'couleur' => $voiture->getCouleur(),
            'prix' => $voiture->getPrix(),
            'disponibilite' => $voiture->getDisponibilite(),
            'image' => $voiture->getImage(),
        ];
    }

    // Return a JSON response with the data
    return new JsonResponse(['data' => $data]);
}
}