<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use Knp\Component\Pager\PaginatorInterface;
use Twilio\Rest\Client as TwilioClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use Dompdf\Options;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options as XLSXOptions;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Cell; // Added for cell-specific styling
use OpenSpout\Common\Entity\Style\Color; // Added for color definitions

final class CommandeController extends AbstractController
{
    private TwilioClient $twilioClient;

    public function __construct(TwilioClient $twilioClient)
    {
        $this->twilioClient = $twilioClient;
    }

    #[Route('/commande', name: 'app_commande')]
    public function index(
        CommandeRepository $commandeRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $filter = $request->query->get('filter', 'all');
        $sort = $request->query->get('sort', 'c.date_com');
        $direction = $request->query->get('direction', 'DESC');
    
        $queryBuilder = $commandeRepository->createQueryBuilder('c')
            ->leftJoin('c.id', 'u')
            ->addSelect('u');
    
        if ($filter !== 'all') {
            $queryBuilder->andWhere('c.status = :status')
                ->setParameter('status', $filter);
        }
    
        $validSortFields = ['c.date_com', 'u.nom'];
        $sortField = in_array($sort, $validSortFields) ? $sort : 'c.date_com';
        $sortDirection = in_array(strtoupper($direction), ['ASC', 'DESC']) ? $direction : 'DESC';
        
        $queryBuilder->orderBy($sortField, $sortDirection);
    
        $commandes = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10,
            [
                'wrap-queries' => true,
                'sortFieldWhitelist' => $validSortFields
            ]
        );
    
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
            'currentFilter' => $filter,
            'currentSort' => $sortField,
            'currentDirection' => $sortDirection
        ]);
    }

    #[Route('/commande/search', name: 'app_commande_search', methods: ['GET'])]
    public function search(CommandeRepository $commandeRepository, Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->query->get('q', '');
            $filter = $request->query->get('filter', 'all');
    
            $queryBuilder = $commandeRepository->createQueryBuilder('c')
                ->join('c.id', 'u')
                ->addSelect('u')
                ->where('u.nom LIKE :search OR u.prenom LIKE :search')
                ->setParameter('search', '%'.$searchTerm.'%');
    
            if ($filter !== 'all') {
                $queryBuilder->andWhere('c.status = :status')
                    ->setParameter('status', $filter);
            }
    
            $commandes = $queryBuilder
                ->orderBy('c.date_com', 'DESC')
                ->getQuery()
                ->getResult();
    
            $results = [];
            foreach ($commandes as $commande) {
                $user = $commande->getId();
                $results[] = [
                    'id' => $commande->getIdCom(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'tel' => $user->getTel(),
                    'date' => $commande->getDateCom()->format('d/m/Y H:i'),
                    'montant' => number_format($commande->getMontantTotal(), 2),
                    'status' => $commande->getStatus(),
                    'statusClass' => $commande->getStatus() == 'traitée' ? 'success' : 'warning'
                ];
            }
    
            return $this->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
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
    
        $user = $commande->getId();
        $rawNumber = $user->getTel();
        $toNumber = '+216' . $rawNumber;
        $fromNumber = '+17155641167';
        $messageBody = 'Votre commande est traitée. Vous le recevrez dans un délai de 24 heures';
    
        try {
            $message = $this->twilioClient->messages->create(
                $toNumber,
                [
                    'from' => $fromNumber,
                    'body' => $messageBody
                ]
            );
            
            $this->addFlash('success', 'SMS envoyé avec SID: ' . $message->sid);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Échec d\'envoi du SMS: ' . $e->getMessage());
        }
    
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

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        return $this->render('commande/details.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/commande/pdf/{id}', name: 'app_commande_pdf', methods: ['GET'])]
    public function generatePdf(int $id, CommandeRepository $commandeRepository): Response
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

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $html = $this->renderView('commande/pdf_invoice.html.twig', [
            'commande' => $commande,
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('tempDir', sys_get_temp_dir());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'invoice_CMD-' . $commande->getId_com() . '.pdf'
        ));

        return $response;
    }

    #[Route('/commande/export-excel', name: 'app_commande_export_excel', methods: ['GET'])]
    public function exportExcel(CommandeRepository $commandeRepository): Response
    {
        // Fetch all orders with user details
        $commandes = $commandeRepository->createQueryBuilder('c')
            ->select('c', 'u')
            ->join('c.id', 'u')
            ->orderBy('c.date_com', 'DESC')
            ->getQuery()
            ->getResult();

        // Calculate total sales
        $totalSales = 0;
        foreach ($commandes as $commande) {
            $totalSales += $commande->getMontant_total();
        }

        // Create new XLSX writer
        $writer = new Writer();
        $tempFile = tempnam(sys_get_temp_dir(), 'commandes_export') . '.xlsx';
        $writer->openToFile($tempFile);

        // Define styles
        // Header style: dark green background, white text, bold
        $headerStyle = (new Style())
            ->setBackgroundColor('355E3B') // Dark green
            ->setFontColor(Color::WHITE)
            ->setFontBold();

        // Status styles
        $enAttenteStyle = (new Style())
            ->setBackgroundColor('E6E6FA'); // Light purple for "en attente"
        $traiteeStyle = (new Style())
            ->setBackgroundColor('90EE90'); // Light green for "traitée"

        // Row styles for alternating colors
        $evenRowStyle = (new Style())
            ->setBackgroundColor('F5F5F5'); // Light grey for even rows
        $oddRowStyle = (new Style()); // White for odd rows (default)

        // Total row style: bold text
        $totalStyle = (new Style())
            ->setFontBold();

        // Add header row with style
        $headerRow = Row::fromValues([
            'ID Commande',
            'Date',
            'Nom Client',
            'Prénom Client',
            'Téléphone',
            'Adresse',
            'Montant Total (€)',
            'Statut'
        ], $headerStyle);
        $writer->addRow($headerRow);

        // Add data rows with alternating colors and styled Status column
        $rowIndex = 0;
        foreach ($commandes as $commande) {
            $user = $commande->getId();
            $rowStyle = ($rowIndex % 2 === 0) ? $evenRowStyle : $oddRowStyle;

            // Create cells for most columns with the row style
            $cells = [
                Cell::fromValue($commande->getId_com(), $rowStyle),
                Cell::fromValue($commande->getDate_com()->format('d/m/Y H:i'), $rowStyle),
                Cell::fromValue($user->getNom(), $rowStyle),
                Cell::fromValue($user->getPrenom(), $rowStyle),
                Cell::fromValue($user->getTel(), $rowStyle),
                Cell::fromValue($user->getAdresse() ?? 'Non spécifiée', $rowStyle),
                Cell::fromValue(number_format($commande->getMontant_total(), 2), $rowStyle),
            ];

            // Style the Status cell based on the status value
            $status = $commande->getStatus();
            $statusStyle = ($status === 'en attente') ? $enAttenteStyle : $traiteeStyle;
            $cells[] = Cell::fromValue($status, $statusStyle);

            // Create the row with styled cells
            $dataRow = new Row($cells, $rowStyle);
            $writer->addRow($dataRow);

            $rowIndex++;
        }

        // Add empty row for spacing (no styling)
        $emptyRow = Row::fromValues(['', '', '', '', '', '', '', '']);
        $writer->addRow($emptyRow);

        // Add total sales row with bold style
        $totalRow = Row::fromValues([
            '', '', '', '', '', 'Total des ventes :', number_format($totalSales, 2), ''
        ], $totalStyle);
        $writer->addRow($totalRow);

        // Close writer
        $writer->close();

        // Create response
        $response = new BinaryFileResponse($tempFile);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'commandes_export_' . date('Ymd_His') . '.xlsx'
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Delete file after sending
        $response->deleteFileAfterSend(true);

        return $response;
    }
}