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

    /**
     * Sends a conversation to the Gemini API and returns the bot's response.
     *
     * @param array $conversation Array of messages with 'role' (user/assistant) and 'text'
     * @return string The bot's response or an error message
     */
    public function getChatbotResponse(array $conversation): string
    {
        $model = 'gemini-1.5-pro-002';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $this->apiKey;
    
        try {
            $contents = [];
    
            // System instruction to limit chatbot scope
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => 'You are a helpful assistant that only answers questions related to cars. If a question is not related to cars, reply with: "Sorry, I can only answer questions about cars."']
                ]
            ];
    
            // Add user/model messages
            foreach ($conversation as $message) {
                $contents[] = [
                    'role' => $message['role'] === 'user' ? 'user' : 'model',
                    'parts' => [
                        ['text' => $message['text']]
                    ]
                ];
            }
    
            $response = $this->httpClient->request('POST', $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => $contents
                ],
            ]);
    
            $data = $response->toArray(false);
    
            // Log response for debugging
            file_put_contents(__DIR__ . '/../gemini_response.json', json_encode($data, JSON_PRETTY_PRINT));
    
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return $data['candidates'][0]['content']['parts'][0]['text'];
            }
    
            return 'Gemini responded, but no valid message was found.';
        } catch (\Throwable $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    
}