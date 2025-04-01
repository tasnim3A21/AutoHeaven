<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Messagerie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        // Récupérer toutes les réclamations
        $reclamations = $this->entityManager->getRepository(Reclamation::class)->findAll();

        // Récupérer les informations des utilisateurs manuellement
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
                'image' => $reclamation->getImage(), // Ajouter le champ image
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

    #[Route('/reclamation/respond/{id_rec}', name: 'app_reclamation_respond')]
    public function respond(Request $request, int $id_rec): Response
    {
        // Récupérer la réclamation
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id_rec);
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Créer un formulaire pour la réponse
        $message = new Messagerie();
        $form = $this->createFormBuilder(['message' => $message])
            ->add('message', TextareaType::class, [
                'label' => 'Votre réponse',
                'required' => true,
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        if ($request->isXmlHttpRequest()) {
            return $this->render('reclamation_list/respond.html.twig', [
                'reclamation' => $reclamation,
                'form' => $form->createView(),
            ]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le contenu du message
            $messageContent = $form->get('message')->getData();

            // Créer une nouvelle entrée dans la table messagerie
            $message->setIdRec($reclamation);
            $message->setMessage($messageContent);
            $message->setDatemessage(new \DateTime());
            $message->setIdUser(1);
            $message->setSender('admin');
            $message->setReceiver('client');

            // Ajouter le message à la réclamation
            $reclamation->addMessagerie($message);

            // Mettre à jour le statut de la réclamation
            $reclamation->setStatus('repondu');

            // Persister les changements
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
    public function delete(int $id_rec): Response
    {
        // Récupérer la réclamation
        $reclamation = $this->entityManager->getRepository(Reclamation::class)->find($id_rec);
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Supprimer la réclamation (les messages associés seront supprimés grâce à onDelete="CASCADE")
        $this->entityManager->remove($reclamation);
        $this->entityManager->flush();

        $this->addFlash('success', 'Réclamation supprimée avec succès !');
        return new Response('Suppression réussie', 200);
    }
}