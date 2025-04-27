<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyConversionService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * Fetches exchange rates from CurrencyFreaks API.
     *
     * @return array Exchange rates with currency codes as keys
     * @throws \Exception If the API request fails
     */
    public function getExchangeRates(): array
    {
        $endpoint = "https://api.currencyfreaks.com/v2.0/rates/latest?apikey={$this->apiKey}";

        try {
            $response = $this->httpClient->request('GET', $endpoint);
            $data = $response->toArray();

            if (isset($data['rates'])) {
                return $data['rates'];
            }

            throw new \Exception('Invalid response from CurrencyFreaks API.');
        } catch (\Throwable $e) {
            throw new \Exception('Error fetching exchange rates: ' . $e->getMessage());
        }
    }

    /**
     * Converts an amount from TND to the target currency.
     *
     * @param float $amount Amount in TND
     * @param string $targetCurrency Target currency code (e.g., USD, EUR)
     * @return float Converted amount
     * @throws \Exception If conversion fails
     */
    public function convertFromTND(float $amount, string $targetCurrency): float
    {
        if ($targetCurrency === 'TND') {
            return $amount;
        }

        $rates = $this->getExchangeRates();
        if (!isset($rates[$targetCurrency])) {
            throw new \Exception("Currency {$targetCurrency} not supported.");
        }

        // CurrencyFreaks rates are relative to USD, so we need to normalize
        // TND to USD: amount / TND_rate
        // USD to target: (amount / TND_rate) * target_rate
        $tndRate = $rates['TND'];
        $targetRate = $rates[$targetCurrency];

        return ($amount / $tndRate) * $targetRate;
    }
}