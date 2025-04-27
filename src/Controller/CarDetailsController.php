<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class CarDetailsController extends AbstractController
{
    private string $apiKey;

    public function __construct(string $apiNinjasApiKey)
    {
        $this->apiKey = $apiNinjasApiKey;
    }

    #[Route('/car/details', name: 'car_details_index')]
    public function index(Request $request, HttpClientInterface $httpClient): Response
    {
        $make = $request->query->get('make', '');
        $model = $request->query->get('model', '');
        $carDetails = [];
        $error = null;

        if ($make && $model) {
            try {
                $response = $httpClient->request('GET', 'https://api.api-ninjas.com/v1/cars', [
                    'headers' => [
                        'X-Api-Key' => $this->apiKey,
                    ],
                    'query' => [
                        'make' => $make,
                        'model' => $model,
                    ],
                ]);
                $carDetails = $response->toArray();
            } catch (\Exception $e) {
                $error = 'Unable to fetch car details: ' . $e->getMessage();
            }
        }

        return $this->render('car_details/index.html.twig', [
            'make' => $make,
            'model' => $model,
            'carDetails' => $carDetails,
            'error' => $error,
        ]);
    }

    #[Route('/car/details/export-pdf', name: 'car_details_export_pdf')]
    public function exportPdf(Request $request, HttpClientInterface $httpClient): Response
    {
        $make = $request->query->get('make', '');
        $model = $request->query->get('model', '');
        $carDetails = [];
        $error = null;

        if ($make && $model) {
            try {
                $response = $httpClient->request('GET', 'https://api.api-ninjas.com/v1/cars', [
                    'headers' => [
                        'X-Api-Key' => $this->apiKey,
                    ],
                    'query' => [
                        'make' => $make,
                        'model' => $model,
                    ],
                ]);
                $carDetails = $response->toArray();
            } catch (\Exception $e) {
                $error = 'Unable to fetch car details: ' . $e->getMessage();
            }
        }

        // Configure Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);

        // Render the PDF template
        $html = $this->renderView('car_details/export_pdf.html.twig', [
            'make' => $make,
            'model' => $model,
            'carDetails' => $carDetails,
            'error' => $error,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream the PDF
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="recherche-voiture-'.$make.'-'.$model.'.pdf"',
            ]
        );
    }
}