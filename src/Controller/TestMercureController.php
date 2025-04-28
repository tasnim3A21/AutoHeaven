<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class TestMercureController extends AbstractController
{
    #[Route('/test-mercure', name: 'test_mercure')]
    public function testMercure(HubInterface $hub): Response
    {
        $update = new Update(
            'test-topic',
            json_encode(['message' => 'Test de notification Mercure !'])
        );

        $hub->publish($update);

        return new Response('Message publié via Mercure !');
    }
}