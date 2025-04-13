<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Voiture;
use App\Entity\User;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/avis')]
class AvisController extends AbstractController
{
    #[Route('/', name: 'app_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {
        $avis = $avisRepository->findBy([], ['dateavis' => 'DESC']);
        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_avis_index');
        }

        return $this->render('avis/edit.html.twig', [
            'form' => $form->createView(),
            'avis' => $avis,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $avis->getId(), $request->request->get('_token'))) {
            $entityManager->remove($avis);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avis_index');
    }

    #[Route('/new/{id}', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, $id): Response
    {
        $voiture = $em->getRepository(Voiture::class)->find(22); // ou $id
        if (!$voiture) {
            return $this->render('avis/error.html.twig', [
                'message' => 'Voiture non trouvée. Assurez-vous que l\'ID est correct et réessayez.',
            ]);
        }

        $user = $em->getRepository(User::class)->find(40);
        if (!$user) {
            throw $this->createNotFoundException("L'utilisateur avec l'id 10 n'existe pas.");
        }

        $avis = new Avis();
        $avis->setIdV($voiture);
        $avis->setUtilisateur($user);
        $avis->setDateavis(new \DateTime());

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        $errorMessage = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $avis->getCommentaire();
            
            if (!$this->isCommentValid($commentaire, $errorMessage)) {
                // Commentaire invalide : on renvoie le formulaire avec un message d’erreur
                return $this->render('avis/new.html.twig', [
                    'form' => $form->createView(),
                    'voiture' => $voiture,
                    'errorMessage' => $errorMessage,
                ]);
            }

            $em->persist($avis);
            $em->flush();

            return $this->redirectToRoute('app_avis_index');
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture,
        ]);
    }

    private function isCommentValid(string $commentaire, ?string &$errorMessage): bool
    {
        $badWords = ['merde', 'con', 'salope', 'pute', 'connard', 'idiot', 'abruti'];

        foreach ($badWords as $badWord) {
            if (stripos($commentaire, $badWord) !== false) {
                $errorMessage = 'Votre commentaire contient un mot inapproprié.';
                return false;
            }
        }

        if (preg_match('/(.)\1{4,}/', $commentaire)) {
            $errorMessage = 'Votre commentaire semble contenir du spam (répétition de caractères).';
            return false;
        }

        if (strlen($commentaire) < 5) {
            $errorMessage = 'Votre commentaire est trop court.';
            return false;
        }

        if (strlen($commentaire) > 500) {
            $errorMessage = 'Votre commentaire est trop long.';
            return false;
        }

        if (preg_match('/[A-Z]{10,}/', $commentaire)) {
            $errorMessage = 'Veuillez éviter d\'utiliser trop de majuscules.';
            return false;
        }

        return true;
    }
}
