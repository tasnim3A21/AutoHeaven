<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    #[Route('/listeavis', name: 'app_avis_liste', methods: ['GET'])]
    public function liste(AvisRepository $avisRepository): Response
    {
        $avis = $avisRepository->findBy([], ['dateavis' => 'DESC']);
        return $this->render('avis/listeavis.html.twig', [
            'avis' => $avis,
        ]);
    }
    #[Route('/search', name: 'app_avis_search', methods: ['GET', 'POST'])]
    public function search(Request $request, AvisRepository $avisRepository): Response
    {
        $searchKeyword = $request->query->get('searchKeyword');  // Récupérer le mot-clé du champ de recherche
    
        if ($searchKeyword) {
            // Recherche dans les commentaires des avis où le mot-clé est présent
            $avis = $avisRepository->createQueryBuilder('a')
                ->where('a.commentaire LIKE :searchKeyword')
                ->setParameter('searchKeyword', '%' . $searchKeyword . '%')
                ->orderBy('a.dateavis', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            // Si aucun mot-clé n'est fourni, retourner tous les avis
            $avis = $avisRepository->findBy([], ['dateavis' => 'DESC']);
        }
    
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

    #[Route('/new/{id}', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, int $id): Response
    {
        // Récupérer l'utilisateur avec l'ID dynamique (par exemple, 22)
        $user = $em->getRepository(User::class)->find(10);
        if (!$user) {
            throw $this->createNotFoundException("L'utilisateur avec l'ID $id est introuvable.");
        }
    
        $avis = new Avis();
        $avis->setUtilisateur($user) // Associer l'utilisateur récupéré
             ->setDateavis(new \DateTime());
    
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);
    
        $errorMessage = null;
    
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $avis->getCommentaire();
            if (!$this->isCommentValid($commentaire, $errorMessage)) {
                return $this->render('avis/new.html.twig', [
                    'form' => $form->createView(),
                    'errorMessage' => $errorMessage,
                ]);
            }
    
            $em->persist($avis);
            $em->flush();
    
            $note = $avis->getNote();
            $message = $note >= 4
                ? $this->generatePositiveReply($commentaire)
                : $this->generateNegativeReply($commentaire);
    
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_avis_new', ['id' => $id]);  // Redirection avec le même ID
        }
    
        return $this->render('avis/new.html.twig', [
            'form' => $form->createView(),
            'errorMessage' => $errorMessage,
        ]);
    }
    
    

    private function generatePositiveReply(string $comment): string
    {
        if (stripos($comment, 'service') !== false) {
            return "Merci pour votre retour sur notre service ! 😊";
        } elseif (stripos($comment, 'voiture') !== false) {
            return "On est ravi que la voiture vous ait plu ! 🚗";
        }
        return "Merci pour votre avis positif ! 🙌";
    }

    private function generateNegativeReply(string $comment): string
    {
        if (stripos($comment, 'retard') !== false) {
            return "Désolé pour le retard 😓 Nous allons améliorer cela.";
        } elseif (stripos($comment, 'prix') !== false) {
            return "Merci pour votre remarque sur les prix. Nous y réfléchirons.";
        }
        return "Merci pour votre retour. Nous allons tout faire pour nous améliorer ! 🙏";
    }

    /**
     * Valide que le commentaire contient au moins une marque de voiture connue
     */
    private function isCommentValid(string $commentaire, ?string &$errorMessage): bool
    {
        // Mots interdits
        $badWords = ['merde', 'con', 'salope', 'pute', 'connard', 'idiot', 'abruti'];
        foreach ($badWords as $badWord) {
            if (stripos($commentaire, $badWord) !== false) {
                $errorMessage = 'Votre commentaire contient un mot inapproprié.';
                return false;
            }
        }

        // Spam (répétitions)
        if (preg_match('/(.)\1{4,}/', $commentaire)) {
            $errorMessage = 'Votre commentaire semble contenir du spam (répétition de caractères).';
            return false;
        }

        // Longueur
        $len = mb_strlen($commentaire);
        if ($len < 5) {
            $errorMessage = 'Votre commentaire est trop court.';
            return false;
        }
        if ($len > 500) {
            $errorMessage = 'Votre commentaire est trop long.';
            return false;
        }

        // Trop de majuscules
        if (preg_match('/[A-Z]{10,}/', $commentaire)) {
            $errorMessage = 'Veuillez éviter d\'utiliser trop de majuscules.';
            return false;
        }

        // URLs interdites
        if (preg_match('/(https?:\/\/|www\.)\S+/i', $commentaire)) {
            $errorMessage = 'Les URLs ne sont pas autorisées dans les commentaires.';
            return false;
        }

        // Validation des marques
        $brands = ['Jeep', 'BMW', 'Audi', 'Mercedes', 'Ford', 'Peugeot', 'Renault'];
        $found = false;
        foreach ($brands as $brand) {
            if (stripos($commentaire, $brand) !== false) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $errorMessage = 'Votre commentaire doit mentionner une marque de voiture valide (ex: ' . implode(', ', $brands) . ').';
            return false;
        }

        return true;
    }

    #[Route('/similar', name: 'app_avis_similar', methods: ['POST'])]
    public function findSimilarComments(Request $request, AvisRepository $avisRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $inputComment = trim($data['comment'] ?? '');
        if (strlen($inputComment) < 5) {
            return new JsonResponse(['error' => 'Commentaire trop court ou vide.'], 400);
        }

        $allAvis = $avisRepository->findAll();
        $threshold = 0.5;
        $limit = 3;
        $similarAvis = [];

        foreach ($allAvis as $avis) {
            $score = $this->calculateSimilarity($inputComment, $avis->getCommentaire());
            if ($score >= $threshold) {
                $similarAvis[] = [
                    'id' => $avis->getId(),
                    'commentaire' => $avis->getCommentaire(),
                    'note' => $avis->getNote(),
                    'similarity' => round($score, 2),
                ];
            }
        }

        usort($similarAvis, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        $similarAvis = array_slice($similarAvis, 0, $limit);
        return new JsonResponse(['similar' => $similarAvis]);
    }

    private function calculateSimilarity(string $a, string $b): float
    {
        similar_text(mb_strtolower($a), mb_strtolower($b), $percent);
        return $percent / 100.0;
    }

    #[Route('/api/avis/top-clients', name: 'api_avis_top_clients', methods: ['GET'])]
    public function topClients(Request $request, AvisRepository $avisRepository): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 5);
        $clients = $avisRepository->findTopClients($limit);
        return $this->json($clients);
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
}