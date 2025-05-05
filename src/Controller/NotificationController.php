<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;

#[Route('/notification', name: 'app_notifications_')]
final class NotificationController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $em): Response
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/dropdown', name: 'dropdown')]
    public function dropdown(EntityManagerInterface $em): Response
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy([], ['createdAt' => 'DESC'], 10);

        $unreadCount = $em->getRepository(Notification::class)
            ->count(['isRead' => false]);

        return $this->render('notification/dropdown.html.twig', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    #[Route('/{id}/read', name: 'mark_as_read', methods: ['POST'])]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): Response
    {
        $notification->setIsRead(true);
        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }

    #[Route('/mark-all-read', name: 'mark_all_read', methods: ['POST'])]
    public function markAllRead(EntityManagerInterface $em): Response
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy(['isRead' => false]);

        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }

        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }

    #[Route('/clear-all', name: 'clear_all', methods: ['POST'])]
    public function clearAll(EntityManagerInterface $em): Response
    {
        $notifications = $em->getRepository(Notification::class)
            ->findAll();

        foreach ($notifications as $notification) {
            $em->remove($notification);
        }

        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }

    #[Route('/save', name: 'save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? null;

        if (!$message) {
            return new JsonResponse(['status' => 'error', 'message' => 'Message is required'], 400);
        }

        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTime());

        $em->persist($notification);
        $em->flush();

        return new JsonResponse(['status' => 'success', 'id' => $notification->getId()]);
    }

    #[Route('/unread-stock-alerts', name: 'unread_stock_alerts', methods: ['GET'])]
    public function getUnreadStockAlerts(EntityManagerInterface $em): JsonResponse
    {
        $oneDayAgo = new \DateTime('-1 day');
        $notifications = $em->getRepository(Notification::class)
            ->createQueryBuilder('n')
            ->where('n.isRead = :isRead')
            ->andWhere('n.createdAt >= :oneDayAgo')
            ->setParameter('isRead', false)
            ->setParameter('oneDayAgo', $oneDayAgo)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $alerts = [];
        foreach ($notifications as $notification) {
            $alerts[] = [
                'id' => $notification->getId(),
                'message' => $notification->getMessage(),
                'timestamp' => $notification->getCreatedAt()->getTimestamp() * 1000, // Convertir en millisecondes
            ];
        }

        return new JsonResponse(['alerts' => $alerts]);
    }
}