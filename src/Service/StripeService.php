<?php
namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    

    public function __construct(string $privateKey, string $currency)
    {
        $this->privateKey = $privateKey;
        $this->currency = $currency;
        Stripe::setApiKey($privateKey);
    }

    public function createPaymentSession(
        string $successUrl,
        string $cancelUrl,
        array $lineItems,
        array $metadata = []
    ): Session {
        try {
            return Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [$lineItems],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => $metadata,
                'currency' => $this->currency,
            ]);
        } catch (ApiErrorException $e) {
            throw new \RuntimeException('Erreur lors de la création de la session Stripe: '.$e->getMessage());
        }
    }
}