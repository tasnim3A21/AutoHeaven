<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use Knp\Component\Pager\PaginatorInterface;

final class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(
        CommandeRepository $commandeRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $query = $commandeRepository->createQueryBuilder('c')
            ->join('c.id', 'u')
            ->addSelect('u')
            ->orderBy('c.date_com', 'DESC')
            ->getQuery();

        $commandes = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
            'currentFilter' => 'all'
        ]);
    }

  

   
    #[Route('/commande/confirm/{id}', name: 'app_commande_confirm')]
    public function confirm(int $id, EntityManagerInterface $entityManager): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $commande->setStatus('traitée');
        $entityManager->flush();

        $this->addFlash('success', 'Commande #' . $commande->getId_com() . ' confirmée avec succès');
        return $this->redirectToRoute('app_commande');
    }
    #[Route('/commande/details/{id}', name: 'app_commande_details', methods: ['GET'])]
public function details(int $id, CommandeRepository $commandeRepository): Response
{
   
   $commande = $commandeRepository->createQueryBuilder('c')
        ->select('c', 'u', 'lc', 'e')
        ->join('c.id', 'u') 
        ->leftJoin('c.lignecommandes', 'lc') 
        ->leftJoin('lc.id_e', 'e') 
        ->where('c.id_com = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
   /* $commande =$request->query->get('aff', '');*/

    if (!$commande) {
        throw $this->createNotFoundException('Commande non trouvée');
    }

    return $this->render('commande/details.html.twig', [
        'commande' => $commande,
    ]);
}
}