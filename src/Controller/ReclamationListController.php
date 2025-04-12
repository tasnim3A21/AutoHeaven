<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Messagerie;
use App\Form\ReclamationReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class ReclamationListController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/reclamation/list', name: 'app_reclamation_list')]
    public function index(): Response
    {
        $reclamations = $this->entityManager->getRepository(Reclamation::class)->findAll();

        $reclamationsWithUserData = [];
        foreach ($reclamations as $reclamation) {
            $userData = $this->entityManager->getConnection()->fetchAssociative(
                'SELECT nom, prenom, tel, email FROM user WHERE id = ?',
                [$reclamation->getId()]
            );

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
            ];
        }

        return $this->render('reclamation_list/index.html.twig', [
            'controller_name' => 'ReclamationListController',
            'reclamations' => $reclamationsWithUserData,
        ]);
    }

    #[Route('/reclamation/respond/{id_rec}', name: 'app_reclamation_respond', methods: ['GET', 'POST'])]
    public function respond(Request $request, int $id_rec): Response
    {
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id_rec);
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        $message = new Messagerie();
        $form = $this->createForm(ReclamationReponseType::class, $message, [
            'reclamation_titre' => $reclamation->getTitre() ?? 'Titre non défini',
            'reclamation_contenu' => $reclamation->getContenu() ?? 'Contenu non défini',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            error_log('Formulaire soumis pour id_rec=' . $id_rec);
            if ($form->isValid()) {
                error_log('Formulaire valide');

                // Vérifier l'utilisateur
                $userId = 1; // Remplacez par l'ID de l'utilisateur connecté si possible
                $userExists = $this->entityManager->getConnection()->fetchOne(
                    'SELECT COUNT(*) FROM user WHERE id = ?',
                    [$userId]
                );
                if (!$userExists) {
                    error_log('Utilisateur avec id=' . $userId . ' non trouvé');
                    $this->addFlash('error', 'Utilisateur avec id=' . $userId . ' non trouvé');
                    return $this->render('reclamation_list/respond.html.twig', [
                        'reclamation' => $reclamation,
                        'form' => $form->createView(),
                    ]);
                }

                $message->setIdRec($reclamation);
                $message->setMessage($form->get('message')->getData());
                $message->setDatemessage(new \DateTime());
                $message->setIdUser($userId);
                $message->setSender('admin');
                $message->setReceiver('client');

                $reclamation->addMessagerie($message);
                $reclamation->setStatus('repondu');

                try {
                    $this->entityManager->persist($message);
                    $this->entityManager->persist($reclamation);
                    $this->entityManager->flush();
                    error_log('Données enregistrées avec succès');
                    $this->addFlash('success', 'Réponse envoyée avec succès !');
                    return $this->redirectToRoute('app_reclamation_list');
                } catch (\Exception $e) {
                    error_log('Erreur lors de l\'enregistrement : ' . $e->getMessage());
                    $this->addFlash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
                }
            } else {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                error_log('Formulaire invalide : ' . implode(', ', $errors));
                $this->addFlash('error', 'Erreur de validation : ' . implode(', ', $errors));
            }
        } else {
            error_log('Formulaire non soumis pour id_rec=' . $id_rec);
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
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Réclamation non trouvée'], 404);
            }
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        $this->entityManager->remove($reclamation);
        $this->entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true, 'message' => 'Réclamation supprimée']);
        }

        $this->addFlash('success', 'Réclamation supprimée avec succès !');
        return $this->redirectToRoute('app_reclamation_list');
    }
}