<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\PusherService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ChatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private UserRepository $userRepository,
        private MessageRepository $messageRepository,
        private PusherService $pusherService
    ) {}

  /*  #[Route('/chat', name: 'app_chat')]
    public function index(): Response
    {
        $currentUser = $this->security->getUser();
        
        if ($currentUser->getRole() === 'admin') {
            $clients = $this->userRepository->findBy(['role' => 'client']);
            return $this->render('chat/admin.html.twig', [
                'clients' => $clients,
                'pusher_key' => 'a57746eaf2c89e4593e9',
                'pusher_cluster' => 'eu'
            ]);
        }

        $admins = $this->userRepository->findBy(['role' => 'admin']);
        $messages = $admins ? $this->messageRepository->findConversation($currentUser, $admins[0]) : [];

        return $this->render('chat/client.html.twig', [
            'admin' => $admins[0] ?? null,
            'messages' => $messages,
            'pusher_key' => 'a57746eaf2c89e4593e9',
            'pusher_cluster' => 'eu'
        ]);
    }

    #[Route('/chat/send', name: 'app_chat_send', methods: ['POST'])]
    public function send(Request $request): Response
    {
        $currentUser = $this->security->getUser();
        $recipientId = $request->request->get('recipient');
        $content = $request->request->get('content');

        $recipient = $this->userRepository->find($recipientId);

        if (!$recipient) {
            return $this->json(['status' => 'error', 'message' => 'Recipient not found']);
        }

        // Vérification des rôles
        if (($currentUser->getRole() === 'client' && $recipient->getRole() !== 'admin') ||
            ($currentUser->getRole() === 'admin' && $recipient->getRole() !== 'client')) {
            return $this->json(['status' => 'error', 'message' => 'Invalid chat participant']);
        }

        $message = new Message();
        $message->setContent($content)
                ->setSender($currentUser)
                ->setRecipient($recipient);

        $this->em->persist($message);
        $this->em->flush();

        // Déterminer le canal en fonction du rôle
        $channel = $currentUser->getRole() === 'admin' 
            ? 'private-client-' . $recipient->getId()
            : 'private-admin-' . $recipient->getId();

        $this->pusherService->trigger($channel, 'new-message', [
            'id' => $message->getId(),
            'content' => $message->getContent(),
            'sender' => [
                'id' => $currentUser->getId(),
                'username' => $currentUser->getUsername(),
                'role' => $currentUser->getRole(),
                'avatar' => $currentUser->getPhoto_profile() ?? '/default-avatar.png',
            ],
            'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        return $this->json(['status' => 'success']);
    }

    #[Route('/chat/conversation/{userId}', name: 'app_chat_conversation')]
    public function getConversation(User $user): Response
    {
        $currentUser = $this->security->getUser();
        $messages = $this->messageRepository->findConversation($currentUser, $user);

        return $this->json([
            'messages' => array_map(function (Message $message) use ($currentUser) {
                return [
                    'id' => $message->getId(),
                    'content' => $message->getContent(),
                    'sender' => $message->getSender()->getId(),
                    'avatar' => $message->getSender()->getPhoto_profile() ?? '/default-avatar.png',
                    'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                    'isMe' => $message->getSender()->getId() === $currentUser->getId(),
                ];
            }, $messages),
        ]);
    }*/

    #[Route('/chat/typing', name: 'app_chat_typing', methods: ['POST'])]
    public function typing(Request $request): Response
    {
        $currentUser = $this->security->getUser();
        $recipientId = $request->request->get('recipient');
        $recipient = $this->userRepository->find($recipientId);

        if (!$recipient) {
            return $this->json(['status' => 'error', 'message' => 'Recipient not found']);
        }

        $channel = $currentUser->getRole() === 'admin'
            ? 'private-client-' . $recipient->getId()
            : 'private-admin-' . $recipient->getId();

        $this->pusherService->trigger($channel, 'typing', [
            'sender' => $currentUser->getId(),
        ]);

        return $this->json(['status' => 'success']);
    }

    #[Route('/chat/mark-read/{messageId}', name: 'app_chat_mark_read', methods: ['POST'])]
    public function markRead(Message $message): Response
    {
        $currentUser = $this->security->getUser();
        if ($message->getRecipient()->getId() === $currentUser->getId() && !$message->isRead()) {
            $message->setIsRead(true);
            $this->em->flush();

            $channel = $currentUser->getRole() === 'admin'
                ? 'private-client-' . $message->getSender()->getId()
                : 'private-admin-' . $message->getSender()->getId();

            $this->pusherService->trigger($channel, 'message-read', [
                'messageId' => $message->getId(),
            ]);
        }
        return $this->json(['status' => 'success']);
    }
}