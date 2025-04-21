<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyConversionController extends AbstractController
{
    private string $apiKey;

    public function __construct(string $currencyfreaksApiKey)
    {
        $this->apiKey = $currencyfreaksApiKey;
    }

    #[Route('/currency', name: 'currency_conversion')]
public function index(Request $request, HttpClientInterface $httpClient): Response
{
    $amount = $request->query->get('amount', 0);
    $selectedCurrency = $request->query->get('currency', 'TND');
    $convertedAmount = null;
    $error = null;
    $currencies = [];

    try {
        $response = $httpClient->request('GET', 'https://api.currencyfreaks.com/v2.0/rates/latest', [
            'query' => ['apikey' => $this->apiKey],
        ]);
        $data = $response->toArray();

        if (!isset($data['rates'])) {
            throw new \Exception('Invalid response from CurrencyFreaks API.');
        }

        $rates = $data['rates'];
        $currencies = $rates; // All available currencies

        if ($amount > 0 && $selectedCurrency !== 'TND') {
            if (!isset($rates[$selectedCurrency]) || !isset($rates['TND'])) {
                throw new \Exception("Currency {$selectedCurrency} not supported.");
            }

            $convertedAmount = round(($amount / $rates['TND']) * $rates[$selectedCurrency], 2);
        } elseif ($amount > 0 && $selectedCurrency === 'TND') {
            $convertedAmount = round($amount, 2);
        }
    } catch (\Exception $e) {
        $error = 'Unable to convert currency: ' . $e->getMessage();
    }

    return $this->render('CurrencyConversion/index.html.twig', [
        'amount' => $amount,
        'selectedCurrency' => $selectedCurrency,
        'convertedAmount' => $convertedAmount,
        'error' => $error,
        'currencies' => $currencies,
    ]);
}

}