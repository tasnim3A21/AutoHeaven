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

   // src/Service/NotificationService.php
public function notifyStockDepleted(Equipement $equipement, EntityManagerInterface $em): void
{
    // 1. Enregistrer en base
    $notification = new Notification();
    $notification->setEquipement($equipement);
    $notification->setMessage(sprintf(
        'Stock épuisé pour %s (Ref: %s)',
        $equipement->getNom(),
        $equipement->getReference()
    ));
    
    $em->persist($notification);
    $em->flush();

    // 2. Notifier en temps réel
    $this->pusher->trigger('stock-channel', 'stock-depleted', [
        'id' => $notification->getId(),
        'message' => $notification->getMessage(),
        'equipementId' => $equipement->getId()
    ]);
}
}