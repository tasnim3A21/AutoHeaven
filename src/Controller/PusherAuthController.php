<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PusherAuthController extends AbstractController
{
    private string $pusherKey;
    private string $pusherSecret;

    public function __construct(string $pusherKey, string $pusherSecret)
    {
        $this->pusherKey = $pusherKey;
        $this->pusherSecret = $pusherSecret;
    }

    #[Route('/pusher/auth', name: 'app_pusher_auth')]
    public function authenticate(Request $request, Security $security): JsonResponse
    {
        $user = $security->getUser();
        
        if (!$user) {
            return $this->json([], 403);
        }

        $socketId = $request->request->get('socket_id');
        $channelName = $request->request->get('channel_name');

        if (strpos($channelName, 'private-chat-') === 0) {
            $channelUserId = str_replace('private-chat-', '', $channelName);
            if ($channelUserId != $user->getId()) {
                return $this->json([], 403);
            }
        } else {
            return $this->json([], 403);
        }

        $authString = hash_hmac(
            'sha256',
            $socketId . ':' . $channelName,
            $this->pusherSecret
        );

        return $this->json([
            'auth' => $this->pusherKey . ':' . $authString
        ]);
    }
}