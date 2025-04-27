<?php

namespace App\Controller;

use App\Entity\Res_testdrive;
use App\Entity\Res_remorquage;
use App\Entity\Res_mecanicien;
use App\Entity\User;
use App\Entity\Voiture;
use App\Entity\Camion_remorquage;
use App\Entity\Mecanicien;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookingController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/booking', name: 'app_booking')]
    public function index(): Response
    {
        

        $userId = 3;

        // Fetch test drives with vehicle information
        $testDrives = $this->entityManager->createQuery(
            'SELECT t, v 
            FROM App\Entity\Res_testdrive t 
            JOIN App\Entity\Voiture v WITH t.id_v = v.id_v 
            WHERE t.id_u = :userId 
            ORDER BY t.date DESC'
        )->setParameter('userId', $userId)->getResult();

        // Fetch towing requests with agency information
        $remorquages = $this->entityManager->createQuery(
            'SELECT r, cr 
            FROM App\Entity\Res_remorquage r 
            JOIN r.camionRemorquage cr 
            WHERE r.id_u = :userId 
            ORDER BY r.date DESC'
        )->setParameter('userId', $userId)->getResult();

        // Fetch mechanic appointments with mechanic information
        $mecaniciens = $this->entityManager->createQuery(
            'SELECT r, m 
            FROM App\Entity\Res_mecanicien r 
            JOIN r.mecanicien m 
            WHERE r.id_u = :userId'
        )->setParameter('userId', $userId)->getResult();

        return $this->render('booking/index.html.twig', [
            'testDrives' => $testDrives,
            'remorquages' => $remorquages,
            'mecaniciens' => $mecaniciens,
        ]);
    }

}
