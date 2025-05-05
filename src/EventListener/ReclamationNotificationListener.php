<?php

namespace App\EventListener;

use App\Event\ReclamationSubmittedEvent;
use Pusher\Pusher;
use Psr\Log\LoggerInterface;

class ReclamationNotificationListener
{
    private Pusher $pusher;
    private LoggerInterface $logger;

    public function __construct(Pusher $pusher, LoggerInterface $logger)
    {
        $this->pusher = $pusher;
        $this->logger = $logger;
    }

    public function onReclamationSubmitted(ReclamationSubmittedEvent $event): void
    {
        $reclamation = $event->getReclamation();
        $userData = $event->getUserData();

        $data = [
            'id_rec' => $reclamation->getIdRec(),
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'tel' => $userData['tel'],
            'email' => $userData['email'],
            'titre' => $reclamation->getTitre(),
            'contenu' => $reclamation->getContenu(),
            'status' => $reclamation->getStatus(),
            'datecreation' => $reclamation->getDatecreation()->format('Y-m-d H:i:s'),
            'urgent' => $event->isUrgent(), // Utiliser la méthode isUrgent()
            'image' => $reclamation->getImage(),
        ];

        $this->logger->debug('Envoi de la notification Pusher pour la réclamation ID {id_rec}', [
            'id_rec' => $reclamation->getIdRec(),
            'data' => $data,
        ]);

        try {
            $this->pusher->trigger('admin-channel', 'new-reclamation', $data);
            $this->logger->info('Notification Pusher envoyée pour la réclamation ID {id_rec}', [
                'id_rec' => $reclamation->getIdRec(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi de la notification Pusher pour la réclamation ID {id_rec} : {error}', [
                'id_rec' => $reclamation->getIdRec(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}