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
            return new JsonResponse(['success' => false, 'message' => 'Réclamation non trouvée'], 404);
        }

        // Débogage : Vérifier les valeurs de titre et contenu
        error_log('Titre de la réclamation: ' . ($reclamation->getTitre() ?? 'NULL'));
        error_log('Contenu de la réclamation: ' . ($reclamation->getContenu() ?? 'NULL'));

        $message = new Messagerie();
        $form = $this->createForm(ReclamationReponseType::class, $message, [
            'reclamation_titre' => $reclamation->getTitre() ?? 'Titre non défini',
            'reclamation_contenu' => $reclamation->getContenu() ?? 'Contenu non défini',
        ]);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $messageContent = $form->get('message')->getData();
                    error_log('Message content: ' . ($messageContent ?? 'NULL'));

                    $message->setIdRec($reclamation);
                    $message->setMessage($messageContent);
                    $message->setDatemessage(new \DateTime());
                    $message->setIdUser(1);
                    $message->setSender('admin');
                    $message->setReceiver('client');

                    $reclamation->addMessagerie($message);
                    $reclamation->setStatus('repondu');

                    $this->entityManager->persist($message);
                    $this->entityManager->persist($reclamation);
                    $this->entityManager->flush();

                    return new JsonResponse(['success' => true, 'message' => 'Réponse envoyée avec succès']);
                } else {
                    $errors = [];
                    foreach ($form->getErrors(true) as $error) {
                        $errors[] = $error->getMessage();
                    }
                    error_log('Validation errors: ' . json_encode($errors));

                    // Rendre le formulaire avec les erreurs pour un re-rendu côté client
                    $formHtml = $this->renderView('reclamation_list/respond.html.twig', [
                        'reclamation' => $reclamation,
                        'form' => $form->createView(),
                    ]);

                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Erreur de validation',
                        'errors' => $errors,
                        'form' => $formHtml,
                    ], 400);
                }
            }

            return $this->render('reclamation_list/respond.html.twig', [
                'reclamation' => $reclamation,
                'form' => $form->createView(),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $messageContent = $form->get('message')->getData();
            error_log('Message content (non-AJAX): ' . ($messageContent ?? 'NULL'));

            $message->setIdRec($reclamation);
            $message->setMessage($messageContent);
            $message->setDatemessage(new \DateTime());
            $message->setIdUser(1);
            $message->setSender('admin');
            $message->setReceiver('client');

            $reclamation->addMessagerie($message);
            $reclamation->setStatus('repondu');

            $this->entityManager->persist($message);
            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Réponse envoyée avec succès !');
            return $this->redirectToRoute('app_reclamation_list');
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
        return new Response('Suppression réussie', 200);
    }
}