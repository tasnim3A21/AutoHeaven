<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;

class GeminiTranslationService
{
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(string $apiKey, LoggerInterface $logger)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    private function detectLanguage(string $text): ?string
    {
        try {
            $client = HttpClient::create();
            $response = $client->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Detect the language of the following text and return the language code (e.g., 'en', 'fr', 'es'): {$text}"
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $data = $response->toArray();
            $languageCode = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$languageCode) {
                $this->logger->error('No language detected for text: {text}', ['text' => $text]);
                return null;
            }

            return trim($languageCode);
        } catch (\Exception $e) {
            $this->logger->error('Error detecting language for text: {error}', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function translateToTarget(string $text, string $targetLanguage): ?string
    {
        try {
            $client = HttpClient::create();
            $response = $client->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Translate the following text to {$targetLanguage} and provide only the translated text without any explanations, options, or additional commentary: {$text}"
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $data = $response->toArray();
            $translatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$translatedText) {
                $this->logger->error('No translation returned from Gemini API for text: {text}', ['text' => $text]);
                return null;
            }

            // Nettoyer la réponse pour s'assurer qu'aucune explication n'est incluse
            // On prend la première ligne si la réponse contient plusieurs lignes
            $translatedText = trim($translatedText);
            $translatedTextLines = explode("\n", $translatedText);
            $cleanedTranslatedText = trim($translatedTextLines[0]);

            // Supprimer tout texte entre parenthèses (souvent utilisé pour des translittérations ou commentaires)
            $cleanedTranslatedText = preg_replace('/\([^)]+\)/', '', $cleanedTranslatedText);
            // Supprimer tout texte après des caractères comme "*" ou ":" qui pourraient indiquer une explication
            $cleanedTranslatedText = preg_replace('/[\*\:].*$/s', '', $cleanedTranslatedText);
            // Supprimer les espaces inutiles après le nettoyage
            $cleanedTranslatedText = trim($cleanedTranslatedText);

            if (empty($cleanedTranslatedText)) {
                $this->logger->error('Cleaned translation is empty for text: {text}', ['text' => $text]);
                return null;
            }

            return $cleanedTranslatedText;
        } catch (\Exception $e) {
            $this->logger->error('Error translating text with Gemini API: {error}', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function translate(string $text, string $targetLanguage = 'en'): ?string
    {
        // Étape 1 : Détecter la langue source
        $sourceLanguage = $this->detectLanguage($text);
        if (!$sourceLanguage) {
            $this->logger->error('Unable to detect source language for text: {text}', ['text' => $text]);
            return null;
        }

        $this->logger->info('Detected source language: {sourceLanguage}', ['sourceLanguage' => $sourceLanguage]);

        // Si la langue source est déjà la langue cible, retourner le texte tel quel
        if ($sourceLanguage === $targetLanguage) {
            return $text;
        }

        try {
            // Étape 2 : Traduire en anglais si la langue source n'est pas l'anglais
            $intermediateText = $text;
            if ($sourceLanguage !== 'en') {
                $this->logger->info('Translating from {sourceLanguage} to English', ['sourceLanguage' => $sourceLanguage]);
                $intermediateText = $this->translateToTarget($text, 'en');
                if (!$intermediateText) {
                    $this->logger->error('Failed to translate text to English: {text}', ['text' => $text]);
                    return null;
                }
                $this->logger->info('Translated to English: {intermediateText}', ['intermediateText' => $intermediateText]);
            }

            // Étape 3 : Traduire de l'anglais vers la langue cible si la langue cible n'est pas l'anglais
            if ($targetLanguage !== 'en') {
                $this->logger->info('Translating from English to {targetLanguage}', ['targetLanguage' => $targetLanguage]);
                $finalText = $this->translateToTarget($intermediateText, $targetLanguage);
                if (!$finalText) {
                    $this->logger->error('Failed to translate text to {targetLanguage}: {text}', [
                        'targetLanguage' => $targetLanguage,
                        'text' => $intermediateText
                    ]);
                    return null;
                }
                $this->logger->info('Translated to {targetLanguage}: {finalText}', [
                    'targetLanguage' => $targetLanguage,
                    'finalText' => $finalText
                ]);
                return $finalText;
            }

            return $intermediateText;
        } catch (\Exception $e) {
            $this->logger->error('Error during translation process: {error}', ['error' => $e->getMessage()]);
            return null;
        }
    }
}