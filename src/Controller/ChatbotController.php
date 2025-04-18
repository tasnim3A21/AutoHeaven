<?php

namespace App\Controller;

use App\Service\ChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{
    #[Route('/chat', name: 'chatbot')]
    public function chat(Request $request, SessionInterface $session): Response
    {
        // Initialize conversation from session or create new
        $conversation = $session->get('conversation', []);

        return $this->render('chat/index.html.twig', [
            'conversation' => $conversation,
        ]);
    }

    #[Route('/chat/message', name: 'chatbot_message', methods: ['POST'])]
    public function sendMessage(Request $request, ChatbotService $chatbotService, SessionInterface $session): JsonResponse
    {
        $userMessage = $request->request->get('message');
        $conversation = $session->get('conversation', []);

        if ($userMessage) {
            // Add user message to conversation
            $conversation[] = [
                'role' => 'user',
                'text' => $userMessage,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];

            // Get bot response
            $botResponse = $chatbotService->getChatbotResponse($conversation);

            // Add bot response to conversation
            $conversation[] = [
                'role' => 'assistant',
                'text' => $botResponse,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];

            // Save conversation to session
            $session->set('conversation', $conversation);
        }

        return new JsonResponse([
            'conversation' => $conversation,
        ]);
    }

    #[Route('/chat/clear', name: 'chatbot_clear', methods: ['POST'])]
    public function clearChat(SessionInterface $session): JsonResponse
    {
        // Clear conversation from session
        $session->set('conversation', []);

        return new JsonResponse(['status' => 'cleared']);
    }
}