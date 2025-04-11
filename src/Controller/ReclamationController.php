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
        $reclamation->setId(3); // ID statique 3 pour la clé étrangère

        // Créer le formulaire directement avec createFormBuilder
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
            ->add('imageFile', FileType::class, [
                'label' => false,
                'mapped' => false, // Ne pas mapper directement à l'entité
                'required' => false,
                'attr' => [
                    'id' => 'form_imageFile', // S'assurer que l'ID correspond
                    'class' => 'form-file-input',
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Gérer l'upload de l'image AVANT la validation du formulaire
            $imageFile = $form->get('imageFile')->getData(); // Récupérer l'objet UploadedFile depuis le formulaire
            if ($imageFile) {
                // Générer un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le répertoire public/uploads/reclamations
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/reclamations',
                        $newFilename
                    );
                    // Stocker le nom du fichier dans l'entité avant la validation
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
                // Enregistrer dans la base de données
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

                // Réponse pour les requêtes AJAX
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse([
                        'success' => true,
                        'message' => 'Réclamation enregistrée avec succès !'
                    ]);
                }

                // Réponse pour les soumissions classiques
                $this->addFlash('success', 'Votre réclamation a été envoyée avec succès !');
                return $this->redirectToRoute('app_reclamation');
            } else {
                // Si le formulaire n'est pas valide, retourner les erreurs pour les requêtes AJAX
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
}