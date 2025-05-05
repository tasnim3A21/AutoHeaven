<?php

namespace App\Service;

use Pusher\Pusher;
use App\Entity\Equipement;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;
use Doctrine\ORM\Mapping as ORM;

class NotificationService
{
    private Pusher $pusher;

    public function __construct(
        string $pusherKey,
        string $pusherSecret,
        string $pusherAppId,
        string $pusherCluster
    ) {
        $this->pusher = new Pusher(
            $pusherKey,
            $pusherSecret,
            $pusherAppId,
            [
                'cluster' => $pusherCluster,
                'useTLS' => true
            ]
        );
    }

    public function notifyStockDepleted(Equipement $equipement, EntityManagerInterface $em): void
    {
        // Vérifier si une notification existe déjà pour cet équipement dans les dernières 5 minutes
        $recentNotification = $em->getRepository(Notification::class)
            ->createQueryBuilder('n')
            ->where('n.equipement = :equipement')
            ->andWhere('n.createdAt >= :recentTime')
            ->setParameter('equipement', $equipement)
            ->setParameter('recentTime', new \DateTime('-5 minutes'))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($recentNotification) {
            // Une notification existe déjà, ne rien faire
            error_log(sprintf('Duplicate notification skipped for equipement %s (ID: %d)', $equipement->getNom(), $equipement->getId()));
            return;
        }

        // Journalisation pour débogage
        error_log(sprintf('Notification triggered for equipement %s (ID: %d)', $equipement->getNom(), $equipement->getId()));

        // Créer et enregistrer la notification
        $notification = new Notification();
        $notification->setEquipement($equipement);
        $notification->setMessage(sprintf(
            'Stock épuisé pour %s (Ref: %s)',
            $equipement->getNom(),
            $equipement->getReference()
        ));
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTime());

        $em->persist($notification);
        $em->flush();

        // Notifier en temps réel via Pusher
        $this->pusher->trigger('stock-channel', 'stock-depleted', [
            'id' => $notification->getId(),
            'message' => $notification->getMessage(),
            'equipementId' => $equipement->getId()
        ]);
    }
}