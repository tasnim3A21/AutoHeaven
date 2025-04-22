<?php
// src/Controller/ReclamationController.php
namespace App\Controller;

use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class ReclamationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/reclamation', name: 'app_reclamation', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $reclamation = new Reclamation();
        $reclamation->setId(3); // Set static user ID to 3 as per requirement

        // Create the form using createFormBuilder
        $form = $this->createFormBuilder($reclamation)
            ->add('titre', TextType::class, [
                'label' => 'Objet',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'Entrez l\'objet de votre réclamation',
                ],
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre réclamation',
                'attr' => [
                    'class' => 'form-textarea',
                    'rows' => 5,
                    'placeholder' => 'Décrivez votre réclamation ici',
                ],
            ])
            ->add('urgent', CheckboxType::class, [
                'label' => 'Réclamation urgente',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox',
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => false,
                'mapped' => false, // Do not map directly to the entity
                'required' => false,
                'attr' => [
                    'id' => 'form_imageFile',
                    'class' => 'form-file-input',
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Handle image upload BEFORE form validation
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/reclamations',
                        $newFilename
                    );
                    $reclamation->setImage($newFilename);
                } catch (\Exception $e) {
                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse([
                            'success' => false,
                            'message' => 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage()
                        ], 500);
                    }
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->render('reclamation/index.html.twig', [
                        'reclamationForm' => $form->createView(),
                    ]);
                }
            }

            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($reclamation);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse([
                            'success' => false,
                            'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()
                        ], 500);
                    }
                    $this->addFlash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
                    return $this->render('reclamation/index.html.twig', [
                        'reclamationForm' => $form->createView(),
                    ]);
                }

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse([
                        'success' => true,
                        'message' => 'Réclamation enregistrée avec succès !'
                    ]);
                }

                $this->addFlash('success', 'Votre réclamation a été envoyée avec succès !');
                return $this->redirectToRoute('app_reclamation');
            } else {
                if ($request->isXmlHttpRequest()) {
                    $errors = [];
                    foreach ($form->getErrors(true) as $error) {
                        $errors[] = $error->getMessage();
                    }
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Erreur de validation : ' . implode(', ', $errors),
                        'errors' => $errors
                    ], 400);
                }
            }
        }

        return $this->render('reclamation/index.html.twig', [
            'reclamationForm' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/history', name: 'app_reclamation_history', methods: ['GET'])]
    public function history(): Response
    {
        // Fetch reclamations for the user with id = 3
        $reclamations = $this->entityManager->getRepository(Reclamation::class)
            ->findBy(['id' => 3], ['datecreation' => 'DESC']);

        $reclamationsWithData = [];
        foreach ($reclamations as $reclamation) {
            $isUrgent = false;
            $urgentKeywords = ['urgente', 'importante'];
            $title = strtolower($reclamation->getTitre() ?? '');
            $content = strtolower($reclamation->getContenu() ?? '');

            foreach ($urgentKeywords as $keyword) {
                if (str_contains($title, $keyword) || str_contains($content, $keyword)) {
                    $isUrgent = true;
                    break;
                }
            }

            $reclamationsWithData[] = [
                'idRec' => $reclamation->getIdRec(),
                'titre' => $reclamation->getTitre(),
                'contenu' => $reclamation->getContenu(),
                'status' => $reclamation->getStatus(),
                'datecreation' => $reclamation->getDatecreation(),
                'urgent' => $isUrgent,
                'image' => $reclamation->getImage(),
            ];
        }

        return $this->render('reclamation/history.html.twig', [
            'reclamations' => $reclamationsWithData,
        ]);
    }

    #[Route('/reclamation/recent', name: 'app_reclamation_recent', methods: ['GET'])]
    public function recentReclamations(): JsonResponse
    {
        try {
            // Fetch the 5 most recent reclamations for user with id = 3
            $reclamations = $this->entityManager->getRepository(Reclamation::class)
                ->findBy(
                    ['id' => 3],
                    ['datecreation' => 'DESC'],
                    5 // Limit to 5 recent reclamations
                );

            $reclamationsData = array_map(function (Reclamation $reclamation) {
                $isUrgent = false;
                $urgentKeywords = ['urgente', 'importante'];
                $title = strtolower($reclamation->getTitre() ?? '');
                $content = strtolower($reclamation->getContenu() ?? '');

                foreach ($urgentKeywords as $keyword) {
                    if (str_contains($title, $keyword) || str_contains($content, $keyword)) {
                        $isUrgent = true;
                        break;
                    }
                }

                return [
                    'id_rec' => $reclamation->getIdRec(), // Ajout de id_rec pour la modale
                    'titre' => $reclamation->getTitre(),
                    'contenu' => $reclamation->getContenu(),
                    'status' => $reclamation->getStatus(),
                    'datecreation' => $reclamation->getDatecreation() ? $reclamation->getDatecreation()->format('d/m/Y H:i') : null,
                    'urgent' => $isUrgent,
                    'image' => $reclamation->getImage(),
                ];
            }, $reclamations);

            return new JsonResponse([
                'success' => true,
                'reclamations' => $reclamationsData
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des réclamations : ' . $e->getMessage()
            ], 500);
        }
    }
}