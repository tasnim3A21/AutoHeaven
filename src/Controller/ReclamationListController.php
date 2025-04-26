<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationReponseType;
use App\Service\GeminiTranslationService;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class ReclamationListController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private MailerService $mailerService;
    private GeminiTranslationService $translationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        MailerService $mailerService,
        GeminiTranslationService $translationService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->mailerService = $mailerService;
        $this->translationService = $translationService;
    }

    #[Route('/reclamation/list', name: 'app_reclamation_list')]
    public function index(): Response
    {
        $reclamations = $this->entityManager->getRepository(Reclamation::class)->findAll();

        $reclamationsWithUserData = [];
        $urgentCount = 0;
        $nonUrgentCount = 0;

        foreach ($reclamations as $reclamation) {
            $idRec = $reclamation->getIdRec();
            // Skip reclamations with invalid id_rec
            if (!is_numeric($idRec) || $idRec <= 0) {
                $this->logger->warning('Invalid id_rec={id_rec} for reclamation, skipping', [
                    'id_rec' => $idRec
                ]);
                continue;
            }

            $userId = $reclamation->getId();
            $userData = $this->entityManager->getConnection()->fetchAssociative(
                'SELECT nom, prenom, tel, email FROM user WHERE id = ?',
                [$userId]
            );
            if (!$userData) {
                $this->logger->warning('No user found for user ID {user_id} in reclamation ID {id_rec}', [
                    'user_id' => $userId,
                    'id_rec' => $idRec
                ]);
            }

            $keywords = ['urgente', 'importante'];
            $title = strtolower($reclamation->getTitre() ?? '');
            $content = strtolower($reclamation->getContenu() ?? '');
            $isUrgent = false;
            foreach ($keywords as $keyword) {
                if (str_contains($title, $keyword) || str_contains($content, $keyword)) {
                    $isUrgent = true;
                    break;
                }
            }

            if ($isUrgent) {
                $urgentCount++;
            } else {
                $nonUrgentCount++;
            }

            $reclamationsWithUserData[] = [
                'id_rec' => $idRec,
                'titre' => $reclamation->getTitre(),
                'contenu' => $reclamation->getContenu(),
                'status' => $reclamation->getStatus(),
                'datecreation' => $reclamation->getDatecreation(),
                'messageries' => $reclamation->getMessageries(),
                'image' => $reclamation->getImage(),
                'nom' => $userData['nom'] ?? 'Inconnu',
                'prenom' => $userData['prenom'] ?? 'Inconnu',
                'tel' => $userData['tel'] ?? 'N/A',
                'email' => $userData['email'] ?? 'N/A',
                'isUrgent' => $isUrgent,
            ];
        }

        return $this->render('reclamation_list/index.html.twig', [
            'controller_name' => 'ReclamationListController',
            'reclamations' => $reclamationsWithUserData,
            'urgentCount' => $urgentCount,
            'nonUrgentCount' => $nonUrgentCount,
        ]);
    }

    #[Route('/reclamation/respond/{id_rec}', name: 'app_reclamation_respond', methods: ['GET', 'POST'], requirements: ['id_rec' => '\d+'])]
    public function respond(Request $request, int $id_rec): Response
    {
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id_rec);
        if (!$reclamation) {
            $this->logger->error('Réclamation non trouvée pour id_rec={id_rec}', ['id_rec' => $id_rec]);
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        $form = $this->createForm(ReclamationReponseType::class, null, [
            'reclamation_titre' => $reclamation->getTitre() ?? 'Titre non défini',
            'reclamation_contenu' => $reclamation->getContenu() ?? 'Contenu non défini',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->logger->info('Formulaire soumis pour id_rec={id_rec}', ['id_rec' => $id_rec]);
            if ($form->isValid()) {
                $this->logger->info('Formulaire valide pour id_rec={id_rec}', ['id_rec' => $id_rec]);

                $userId = 3; // Utilisateur statique ID 3 (admin)
                $userExists = $this->entityManager->getConnection()->fetchOne(
                    'SELECT COUNT(*) FROM user WHERE id = ?',
                    [$userId]
                );
                if (!$userExists) {
                    $this->logger->error('Utilisateur avec id={userId} non trouvé', ['userId' => $userId]);
                    $this->addFlash('error', 'Utilisateur avec ID 3 non trouvé');
                    return $this->render('reclamation_list/respond.html.twig', [
                        'reclamation' => $reclamation,
                        'form' => $form->createView(),
                    ]);
                }

                // Récupérer les données du formulaire
                $messageContent = $form->get('message')->getData();

                // Mettre à jour le statut de la réclamation
                $reclamation->setStatus('repondu');

                // Récupérer les informations du client (email, nom, prénom)
                $clientUserId = $reclamation->getId();
                $this->logger->info('Récupération des données du client pour clientUserId={clientUserId} et id_rec={id_rec}', [
                    'clientUserId' => $clientUserId,
                    'id_rec' => $id_rec
                ]);
                $clientData = $this->entityManager->getConnection()->fetchAssociative(
                    'SELECT email, nom, prenom FROM user WHERE id = ?',
                    [$clientUserId]
                );
                if (!$clientData) {
                    $this->logger->warning('Aucune donnée utilisateur trouvée pour clientUserId={clientUserId} et id_rec={id_rec}', [
                        'clientUserId' => $clientUserId,
                        'id_rec' => $id_rec
                    ]);
                    $clientData = ['email' => 'abidiahlemea@gmail.com', 'nom' => null, 'prenom' => null];
                }

                try {
                    // Enregistrer la réclamation mise à jour
                    $this->entityManager->persist($reclamation);
                    $this->entityManager->flush();
                    $this->logger->info('Statut de la réclamation mis à jour pour id_rec={id_rec}', ['id_rec' => $id_rec]);

                    // Utiliser l'email du client s'il existe, sinon utiliser une adresse par défaut
                    $recipientEmail = !empty($clientData['email']) ? $clientData['email'] : 'abidiahlemea@gmail.com';

                    // Préparer le nom et prénom du client pour la salutation
                    $clientName = 'Client';
                    if (!empty($clientData['nom']) && !empty($clientData['prenom'])) {
                        $clientName = $clientData['prenom'] . ' ' . $clientData['nom'];
                    } elseif (!empty($clientData['nom'])) {
                        $clientName = $clientData['nom'];
                    } elseif (!empty($clientData['prenom'])) {
                        $clientName = $clientData['prenom'];
                    }

                    // Formatter le message avec la salutation et le texte fixe
                    $formattedMessage = "Bonjour " . $clientName . ",\n\n" . $messageContent . "\n\nSi vous avez d'autres questions, n'hésitez pas à nous contacter.";

                    $this->logger->info('Tentative d\'envoi d\'email à {email} pour id_rec={id_rec}', [
                        'email' => $recipientEmail,
                        'id_rec' => $id_rec
                    ]);
                    $this->mailerService->sendEmail(
                        $recipientEmail,
                        'Réponse à votre réclamation',
                        $formattedMessage,
                        'admin',
                        'client',
                        $id_rec,
                        $userId
                    );
                    $this->logger->info('Email envoyé avec succès à {email} pour id_rec={id_rec}', [
                        'email' => $recipientEmail,
                        'id_rec' => $id_rec
                    ]);
                    $this->addFlash('success', 'Réponse envoyée avec succès et email transmis à ' . $recipientEmail . ' !');

                    return $this->redirectToRoute('app_reclamation_list');
                } catch (\Exception $e) {
                    $this->logger->error('Erreur pour id_rec={id_rec} : {error}', [
                        'id_rec' => $id_rec,
                        'error' => $e->getMessage()
                    ]);
                    $this->addFlash('error', 'Erreur : ' . $e->getMessage());
                }
            } else {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                $this->logger->error('Formulaire invalide pour id_rec={id_rec} : {errors}', [
                    'id_rec' => $id_rec,
                    'errors' => implode(', ', $errors)
                ]);
                $this->addFlash('error', 'Erreur de validation : ' . implode(', ', $errors));
            }
        } else {
            $this->logger->info('Formulaire non soumis pour id_rec={id_rec}', ['id_rec' => $id_rec]);
        }

        return $this->render('reclamation_list/respond.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/delete/{id_rec}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, int $id_rec): Response
    {
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id_rec);
        if (!$reclamation) {
            $this->logger->error('Réclamation non trouvée pour id_rec={id_rec}', ['id_rec' => $id_rec]);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Réclamation non trouvée'], 404);
            }
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        try {
            $this->entityManager->remove($reclamation);
            $this->entityManager->flush();
            $this->logger->info('Réclamation supprimée avec succès pour id_rec={id_rec}', ['id_rec' => $id_rec]);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Réclamation supprimée']);
            }

            $this->addFlash('success', 'Réclamation supprimée avec succès !');
            return $this->redirectToRoute('app_reclamation_list');
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la suppression pour id_rec={id_rec} : {error}', [
                'id_rec' => $id_rec,
                'error' => $e->getMessage()
            ]);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
            }
            $this->addFlash('error', 'Erreur lors de la suppression de la réclamation.');
            return $this->redirectToRoute('app_reclamation_list');
        }
    }

    #[Route('/reclamation/preview-translate/{id_rec}/{targetLang}', name: 'app_reclamation_preview_translate', methods: ['GET'])]
    public function previewTranslate(int $id_rec, string $targetLang): JsonResponse
    {
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id_rec);
        if (!$reclamation) {
            $this->logger->error('Réclamation non trouvée pour id_rec={id_rec}', ['id_rec' => $id_rec]);
            return new JsonResponse(['success' => false, 'message' => 'Réclamation non trouvée'], 404);
        }

        $supportedLanguages = ['en', 'fr', 'es', 'de', 'it', 'ar'];
        if (!in_array($targetLang, $supportedLanguages)) {
            $this->logger->error('Langue cible non supportée: {targetLang}', ['targetLang' => $targetLang]);
            return new JsonResponse(['success' => false, 'message' => 'Langue cible non supportée'], 400);
        }

        try {
            $titre = $reclamation->getTitre();
            $contenu = $reclamation->getContenu();

            $translatedTitre = null;
            $translatedContenu = null;

            if ($titre) {
                $translatedTitre = $this->translationService->translate($titre, $targetLang);
                if (!$translatedTitre) {
                    $this->logger->warning('Échec de la traduction du titre pour id_rec={id_rec} en {targetLang}', [
                        'id_rec' => $id_rec,
                        'targetLang' => $targetLang
                    ]);
                }
            }

            if ($contenu) {
                $translatedContenu = $this->translationService->translate($contenu, $targetLang);
                if (!$translatedContenu) {
                    $this->logger->warning('Échec de la traduction du contenu pour id_rec={id_rec} en {targetLang}', [
                        'id_rec' => $id_rec,
                        'targetLang' => $targetLang
                    ]);
                }
            }

            return new JsonResponse([
                'success' => true,
                'translatedTitre' => $translatedTitre,
                'translatedContenu' => $translatedContenu
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'aperçu de la traduction pour id_rec={id_rec} en {targetLang}: {error}', [
                'id_rec' => $id_rec,
                'targetLang' => $targetLang,
                'error' => $e->getMessage()
            ]);
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de l\'aperçu de la traduction: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/reclamation/confirm-translate/{id_rec}/{targetLang}', name: 'app_reclamation_confirm_translate', methods: ['POST'])]
    public function confirmTranslate(int $id_rec, string $targetLang): JsonResponse
    {
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id_rec);
        if (!$reclamation) {
            $this->logger->error('Réclamation non trouvée pour id_rec={id_rec}', ['id_rec' => $id_rec]);
            return new JsonResponse(['success' => false, 'message' => 'Réclamation non trouvée'], 404);
        }

        $supportedLanguages = ['en', 'fr', 'es', 'de', 'it', 'ar'];
        if (!in_array($targetLang, $supportedLanguages)) {
            $this->logger->error('Langue cible non supportée: {targetLang}', ['targetLang' => $targetLang]);
            return new JsonResponse(['success' => false, 'message' => 'Langue cible non supportée'], 400);
        }

        try {
            $titre = $reclamation->getTitre();
            $contenu = $reclamation->getContenu();

            $translatedTitre = null;
            $translatedContenu = null;

            if ($titre) {
                $translatedTitre = $this->translationService->translate($titre, $targetLang);
                if ($translatedTitre) {
                    $reclamation->setTitre($translatedTitre);
                    $this->logger->info('Titre traduit pour id_rec={id_rec} en {targetLang}: {translated}', [
                        'id_rec' => $id_rec,
                        'targetLang' => $targetLang,
                        'translated' => $translatedTitre
                    ]);
                } else {
                    $this->logger->warning('Échec de la traduction du titre pour id_rec={id_rec} en {targetLang}', [
                        'id_rec' => $id_rec,
                        'targetLang' => $targetLang
                    ]);
                }
            }

            if ($contenu) {
                $translatedContenu = $this->translationService->translate($contenu, $targetLang);
                if ($translatedContenu) {
                    $reclamation->setContenu($translatedContenu);
                    $this->logger->info('Contenu traduit pour id_rec={id_rec} en {targetLang}: {translated}', [
                        'id_rec' => $id_rec,
                        'targetLang' => $targetLang,
                        'translated' => $translatedContenu
                    ]);
                } else {
                    $this->logger->warning('Échec de la traduction du contenu pour id_rec={id_rec} en {targetLang}', [
                        'id_rec' => $id_rec,
                        'targetLang' => $targetLang
                    ]);
                }
            }

            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'translatedTitre' => $translatedTitre,
                'translatedContenu' => $translatedContenu
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la confirmation de la traduction pour id_rec={id_rec} en {targetLang}: {error}', [
                'id_rec' => $id_rec,
                'targetLang' => $targetLang,
                'error' => $e->getMessage()
            ]);
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la confirmation de la traduction: ' . $e->getMessage()], 500);
        }
    }
}