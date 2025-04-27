<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CarDetailsService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function fetchCarDetails(string $make, string $model): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://api.api-ninjas.com/v1/cars', [
                'headers' => [
                    'X-Api-Key' => $this->apiKey,
                ],
                'query' => [
                    'make' => $make,
                    'model' => $model,
                ],
            ]);
            return $response->toArray();
        } catch (\Throwable $e) {
            throw new \Exception('Error fetching car details: ' . $e->getMessage());
        }
    }
}
