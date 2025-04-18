<?php

namespace App\Controller;

use App\Service\ChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{
    #[Route('/chat', name: 'chatbot')]
    public function chat(Request $request, ChatbotService $chatbotService): Response
    {
        $userMessage = $request->query->get('message');
        $botResponse = null;

        if ($userMessage) {
            $botResponse = $chatbotService->getChatbotResponse($userMessage);
        }

        return $this->render('chat/index.html.twig', [
            'userMessage' => $userMessage,
            'botResponse' => $botResponse,
        ]);
    }
}
