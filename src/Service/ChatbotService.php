<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatbotService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function getChatbotResponse(string $message): string
    {
        $model = 'gemini-1.5-pro-002'; // Change if you want another model
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $this->apiKey;

        try {
            $response = $this->httpClient->request('POST', $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $message]
                            ]
                        ]
                    ]
                ],
            ]);

            $data = $response->toArray(false); // Keep HTTP status codes

            // Optionally log response to file
            file_put_contents(__DIR__ . '/../gemini_response.json', json_encode($data, JSON_PRETTY_PRINT));

            // Extract response text safely
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return $data['candidates'][0]['content']['parts'][0]['text'];
            }

            return 'Gemini responded, but no valid message was found.';

        } catch (\Throwable $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
