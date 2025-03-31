<?php

namespace App\Controller;

use App\Entity\Reclamation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class ReclamationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(Request $request): Response
    {
        $reclamation = new Reclamation();
        
        $reclamation->setDatecreation(new \DateTime());
        $reclamation->setId(3); // ID statique 3

        $form = $this->createFormBuilder($reclamation)
            ->add('titre', TextType::class, ['label' => 'Objet'])
            ->add('contenu', TextareaType::class, ['label' => 'Votre réclamation'])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre réclamation a été envoyée avec succès !');
            return $this->redirectToRoute('app_reclamation');
        }

        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
            'reclamationForm' => $form->createView(),
        ]);
    }
}