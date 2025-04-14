<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

final class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function showClients(EntityManagerInterface $em): Response
    {
        // Récupérer les mécaniciens
        $clients = $em->getRepository(User::class)->findBy(['role' => 'client']);
    
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
        ]);
    }


    #[Route('/client/bannir/{id}', name: 'bannir_client')]
    public function bannirClient(int $id, EntityManagerInterface $em): Response
    {
        $client = $em->getRepository(User::class)->find($id);

        if (!$client) {
            throw $this->createNotFoundException('Client non trouvé');
        }

        $now = new \DateTime();
        $client->setBan('oui:' . $now->format('Y-m-d H:i:s'));
        $em->flush();

        $this->addFlash('success', 'Client banni avec succès pour 1 heure.');

        return $this->redirectToRoute('app_client');
    }


    #[Route('/client/supprimer/{id}', name: 'supprimer_client')]
public function supprimerClient(int $id, EntityManagerInterface $em): Response
{
    // Find the mechanic by id
    $client = $em->getRepository(User::class)->find($id);
    if (!$client) {
        // If no mechanic found, throw a 404 exception
        throw $this->createNotFoundException('Client non trouvé');
    }

    // Remove the mechanic from the database
    $em->remove($client);
    $em->flush();

    // Redirect to the list of mechanics
    return $this->redirectToRoute('app_client');
}
}
