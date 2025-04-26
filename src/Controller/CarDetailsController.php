<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
}
