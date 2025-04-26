<?php
// src/Controller/ReclamationController.php
namespace App\Controller;

use App\Entity\Reclamation;
use App\Event\ReclamationSubmittedEvent;
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ReclamationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[Route('/reclamation', name: 'app_reclamation', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $reclamation = new Reclamation();
        $reclamation->setId(3); // ID statique 3 comme spécifié

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
                'mapped' => false,
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
                    $reclamation->setDatecreation(new \DateTime());
                    $this->entityManager->persist($reclamation);
                    $this->entityManager->flush();

                    // Déclencher l'événement de soumission de réclamation
                    $event = new ReclamationSubmittedEvent($reclamation);
                    $this->eventDispatcher->dispatch($event, ReclamationSubmittedEvent::NAME);
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

    #[Route('/reclamation/list', name: 'app_reclamation_list', methods: ['GET'])]
    public function list(): Response
    {
        // Récupérer toutes les réclamations
        $reclamations = $this->entityManager->getRepository(Reclamation::class)->findAll();

        // Préparer les données pour le template
        $reclamationsData = [];
        $urgentCount = 0;
        $nonUrgentCount = 0;

        // Définir les informations statiques de l'utilisateur avec ID 3
        $userStaticData = [
            'nom' => 'ahlem',
            'prenom' => 'ahlem',
            'tel' => '58732503',
            'email' => 'ahlemhidri724@gmail.com',
        ];

        foreach ($reclamations as $reclamation) {
            // Vérifier si la réclamation est urgente en fonction de la case à cocher ou des mots-clés
            $title = strtolower($reclamation->getTitre() ?? '');
            $content = strtolower($reclamation->getContenu() ?? '');
            $isUrgent = $reclamation->isUrgent() || str_contains($title, 'urgente') || str_contains($content, 'urgente') || str_contains($title, 'importante') || str_contains($content, 'importante');

            // Ajouter les informations de l'utilisateur uniquement si l'ID de la réclamation correspond à 3
            $reclamationsData[] = [
                'id_rec' => $reclamation->getIdRec(),
                'nom' => $reclamation->getId() === 3 ? $userStaticData['nom'] : 'N/A',
                'prenom' => $reclamation->getId() === 3 ? $userStaticData['prenom'] : 'N/A',
                'tel' => $reclamation->getId() === 3 ? $userStaticData['tel'] : 'N/A',
                'email' => $reclamation->getId() === 3 ? $userStaticData['email'] : 'N/A',
                'titre' => $reclamation->getTitre(),
                'contenu' => $reclamation->getContenu(),
                'status' => $reclamation->getStatus(),
                'datecreation' => $reclamation->getDatecreation(),
                'urgent' => $isUrgent, // Changé de 'isUrgent' à 'urgent' pour correspondre au template
                'image' => $reclamation->getImage(),
            ];

            if ($isUrgent) {
                $urgentCount++;
            } else {
                $nonUrgentCount++;
            }
        }

        return $this->render('reclamation_admin/index.html.twig', [
            'reclamations' => $reclamationsData,
            'urgentCount' => $urgentCount,
            'nonUrgentCount' => $nonUrgentCount,
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
                'nom' => 'N/A',
                'prenom' => 'N/A',
                'tel' => 'N/A',
                'email' => 'N/A',
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
                    5
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
                    'id_rec' => $reclamation->getIdRec(),
                    'nom' => 'N/A',
                    'prenom' => 'N/A',
                    'tel' => 'N/A',
                    'email' => 'N/A',
                    'titre' => $reclamation->getTitre(),
                    'contenu' => $reclamation->getContenu(),
                    'status' => $reclamation->getStatus(),
                    'datecreation' => $reclamation->getDatecreation() ? $reclamation->getDatecreation()->format('d/m/Y H:i') : null,
                    'urgent' => $isUrgent,
                    'image' => $reclamation->getImage(),
                    'hasResponse' => !$reclamation->getMessageries()->isEmpty(),
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

    #[Route('/reclamation/edit', name: 'app_reclamation_edit', methods: ['POST'])]
    public function edit(Request $request): JsonResponse
    {
        try {
            // Validate id_rec
            $idRec = $request->request->get('id_rec');
            if (!$idRec) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'ID de la réclamation manquant.'
                ], 400);
            }

            // Fetch the reclamation
            $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($idRec);
            if (!$reclamation) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Réclamation non trouvée.'
                ], 404);
            }

            // Check if the reclamation belongs to user with id = 3
            if ($reclamation->getId() !== 3) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à modifier cette réclamation.'
                ], 403);
            }

            // Check if status is 'en_attente'
            if ($reclamation->getStatus() !== 'en_attente') {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Cette réclamation ne peut plus être modifiée car son statut n\'est plus en attente.'
                ], 403);
            }

            // Check if there are any responses
            if (!$reclamation->getMessageries()->isEmpty()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Cette réclamation ne peut plus être modifiée car elle a déjà reçu une réponse.'
                ], 403);
            }

            // Validate required fields
            $titre = $request->request->get('titre');
            $contenu = $request->request->get('contenu');
            $urgent = $request->request->get('urgent') === 'true' || $request->request->get('urgent') === 'on';

            if (!$titre || !$contenu) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Les champs titre et contenu sont requis.'
                ], 400);
            }

            // Update reclamation fields
            $reclamation->setTitre($titre);
            $reclamation->setContenu($contenu);
            $reclamation->setUrgent($urgent);

            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Réclamation modifiée avec succès !'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la modification de la réclamation : ' . $e->getMessage()
            ], 500);
        }
    }
}