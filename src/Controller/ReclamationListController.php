<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationReponseType;
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

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, MailerService $mailerService)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->mailerService = $mailerService;
    }

    #[Route('/reclamation/list', name: 'app_reclamation_list')]
    public function index(): Response
    {
        $reclamations = $this->entityManager->getRepository(Reclamation::class)->findAll();

        $reclamationsWithUserData = [];
        $urgentCount = 0;
        $nonUrgentCount = 0;

        foreach ($reclamations as $reclamation) {
            $userId = $reclamation->getId();
            $userData = $this->entityManager->getConnection()->fetchAssociative(
                'SELECT nom, prenom, tel, email FROM user WHERE id = ?',
                [$userId]
            );
            if (!$userData) {
                $this->logger->warning('No user found for user ID {user_id} in reclamation ID {id_rec}', [
                    'user_id' => $userId,
                    'id_rec' => $reclamation->getIdRec()
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
                'id_rec' => $reclamation->getIdRec(),
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

    #[Route('/reclamation/respond/{id_rec}', name: 'app_reclamation_respond', methods: ['GET', 'POST'])]
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
                $clientData = $this->entityManager->getConnection()->fetchAssociative(
                    'SELECT email, nom, prenom FROM user WHERE id = ?',
                    [$clientUserId]
                );

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
}