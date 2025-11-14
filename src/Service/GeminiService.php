<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GeminiService
{

    public function __construct(
        private ParameterBagInterface $params
    ) {}

    public function bulkTranslate(array $labelsEn): array
    {
        $geminiApiKey = $this->params->get('google_api_key');


        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $geminiApiKey;

        // 1. Préparation du Prompt
        $inputList = json_encode($labelsEn); // Encoder la liste d'entrée en JSON pour clarté

        // Instruction : Demander un retour JSON spécifique
        $prompt = <<<PROMPT
    Vous êtes un traducteur expert. Traduisez les termes français de la liste suivante.
    Le format de la réponse doit être un tableau JSON d'objets où chaque objet contient les clés "en" (le terme original) et "fr" (la traduction française).
    Ne réponds qu'avec le JSON, sans explications, texte ou mise en forme supplémentaire.
    
    Liste à traduire (JSON): $inputList
    PROMPT;

        $requestData = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'temperature' => 0.0, // Toujours 0.0 pour une tâche factuelle/de traduction
                'maxOutputTokens' => 2000  // Augmenter la limite pour la liste
            ]
            // Nous n'utilisons pas le champ 'responseMimeType' car il n'est pas disponible dans l'API REST v1beta.
        ];

        // 2. Appel cURL
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        $response = curl_exec($ch);
        curl_close($ch);

        $responseArray = json_decode($response, true);

        dd($responseArray);
        // 3. Analyse de la Réponse
        if (isset($responseArray['candidates'][0]['content']['parts'][0]['text'])) {
            $jsonText = trim($responseArray['candidates'][0]['content']['parts'][0]['text']);

            // Tenter de décoder la chaîne JSON retournée par Gemini
            $translations = json_decode($jsonText, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($translations)) {
                $resultMap = [];
                foreach ($translations as $item) {
                    // S'assurer que les clés 'en' et 'fr' existent
                    if (isset($item['en']) && isset($item['fr'])) {
                        $resultMap[$item['en']] = $item['fr'];
                    }
                }
                return $resultMap;
            } else {
                // Si l'analyse JSON échoue, cela signifie que Gemini a ajouté du texte non désiré.
                // On peut tenter une méthode de secours ou logguer l'erreur.
                error_log("Gemini a retourné un JSON invalide: " . $jsonText);
                return [];
            }
        }

        return []; // En cas d'échec de la requête
    }
}
