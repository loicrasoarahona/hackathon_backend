<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GeminiService
{

    public function __construct(
        private ParameterBagInterface $params
    ) {}

    public function bulkTranslate(array $labelsEn): array
    {
        $cloudTranslationApiKey = $this->params->get('google_api_key');

        if (empty($labelsEn)) {
            return [];
        }

        // L'URL de l'API Cloud Translation (endpoint de base)
        $apiUrl = "https://translation.googleapis.com/language/translate/v2?key=" . $cloudTranslationApiKey;

        // 1. Préparation des Données
        $requestData = [
            'q' => $labelsEn, // Tableau des termes à traduire
            'target' => 'fr', // Langue cible : Français
            'source' => 'en', // Langue source : Anglais
            'format' => 'text' // Format du contenu (pas HTML)
        ];

        // 2. Appel cURL
        $ch = curl_init($apiUrl);

        // Pour POST, cURL attend une chaîne encodée en URL pour l'API v2,
        // MAIS l'API v2 accepte également le JSON si l'en-tête est défini. 
        // Utilisons le JSON pour une meilleure compatibilité avec la v3 ou si 'q' est trop long.
        $json_data = json_encode($requestData);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Erreur cURL : ' . curl_error($ch));
        }
        curl_close($ch);

        // 3. Analyse de la Réponse
        $responseArray = json_decode($response, true);

        // Gestion des erreurs de l'API (ex: clé invalide, quota dépassé)
        if (isset($responseArray['error'])) {
            $errorMessage = $responseArray['error']['message'] ?? 'Erreur inconnue de l\'API Cloud Translation.';
            throw new Exception("Erreur Cloud Translation: " . $errorMessage);
        }

        // 4. Mapping des Résultats
        $translations = [];

        if (isset($responseArray['data']['translations'])) {
            $translatedResults = $responseArray['data']['translations'];

            // L'API retourne les traductions dans l'ordre exact des termes soumis.
            foreach ($labelsEn as $index => $labelEn) {
                $labelFr = $translatedResults[$index]['translatedText'] ?? "Traduction manquante";

                // L'API peut parfois retourner des entités HTML (ex: &#39; pour apostrophe).
                // On utilise html_entity_decode pour nettoyer la traduction.
                $translations[$labelEn] = html_entity_decode($labelFr, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        dd($translations);

        return $translations;
    }

    // {
    //     $geminiApiKey = $this->params->get('google_api_key');


    //     $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $geminiApiKey;

    //     // 1. Préparation du Prompt
    //     $inputList = json_encode($labelsEn); // Encoder la liste d'entrée en JSON pour clarté

    //     // Instruction : Demander un retour JSON spécifique
    //     $prompt = <<<PROMPT
    // Vous êtes un traducteur expert. Traduisez les termes français de la liste suivante.
    // Le format de la réponse doit être un tableau JSON d'objets où chaque objet contient les clés "en" (le terme original) et "fr" (la traduction française).
    // Ne réponds qu'avec le JSON, sans explications, texte ou mise en forme supplémentaire.

    // Liste à traduire (JSON): $inputList
    // PROMPT;

    //     $requestData = [
    //         'contents' => [['parts' => [['text' => $prompt]]]],
    //         'generationConfig' => [
    //             'temperature' => 0.0, // Toujours 0.0 pour une tâche factuelle/de traduction
    //             'maxOutputTokens' => 2000  // Augmenter la limite pour la liste
    //         ]
    //         // Nous n'utilisons pas le champ 'responseMimeType' car il n'est pas disponible dans l'API REST v1beta.
    //     ];

    //     // 2. Appel cURL
    //     $ch = curl_init($apiUrl);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $responseArray = json_decode($response, true);

    //     // 3. Analyse de la Réponse
    //     if (isset($responseArray['candidates'][0]['content']['parts'][0]['text'])) {
    //         $jsonText = trim($responseArray['candidates'][0]['content']['parts'][0]['text']);
    //         // Tenter de décoder la chaîne JSON retournée par Gemini
    //         $clean = preg_replace('/```json|```/', '', $jsonText);
    //         $clean = trim($clean);
    //         $translations = json_decode($clean, true);
    //         if (json_last_error() === JSON_ERROR_NONE && is_array($translations)) {
    //             $resultMap = [];
    //             foreach ($translations as $item) {
    //                 // S'assurer que les clés 'en' et 'fr' existent
    //                 if (isset($item['en']) && isset($item['fr'])) {
    //                     $resultMap[$item['en']] = $item['fr'];
    //                 }
    //             }
    //             return $resultMap;
    //         } else {
    //             // Si l'analyse JSON échoue, cela signifie que Gemini a ajouté du texte non désiré.
    //             // On peut tenter une méthode de secours ou logguer l'erreur.
    //             error_log("Gemini a retourné un JSON invalide: " . $jsonText);
    //             return [];
    //         }
    //     } else {
    //         throw new Exception("Format de retour invalide");
    //     }

    //     return []; // En cas d'échec de la requête
    // }
}
