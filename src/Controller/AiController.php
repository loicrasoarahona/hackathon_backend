<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use OpenAI\Client;
use OpenAI\Contracts\TransporterContract;

#[Route('/ai')]
class AiController extends AbstractController
{

    public function __construct() {}

    #[Route('/test_gpt', methods: ['GET'])]
    public function testGpt()
    {

        $apiKey = "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";

        $url = "https://api.openai.com/v1/chat/completions";

        $data = [
            "model" => "gpt-4o-mini", // ou "gpt-4o"
            "messages" => [
                ["role" => "system", "content" => "Tu es un assistant utile et concis."],
                ["role" => "user", "content" => "Écris un paragraphe sur les bienfaits de l'apprentissage automatique."],
            ],
            "temperature" => 0.7,
        ];

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey"
        ];


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Erreur cURL : ' . curl_error($ch);
            return new JsonResponse(['message' => 'Erreur', 'error' => curl_error($ch)], 500);
        } else {
            $result = json_decode($response, true);
            // dd($result);
            // $retour = $result["choices"][0]["message"]["content"];
            return new JsonResponse($result);
        }

        curl_close($ch);
    }

    #[Route('/test_google', methods: ['GET'])]
    public function testGoogle()
    {
        $apiKey = "AIzaSyAYYmDBaB37pq9UeEaEqM-5Ag8qpS7J_HM";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=$apiKey";

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => "Écris une phrase inspirante sur la réussite."]
                    ]
                ]
            ]
        ];

        $payload = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => $payload
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo "Erreur CURL: " . curl_error($curl);
            exit;
        }

        curl_close($curl);

        // Convertir la réponse JSON en tableau PHP
        $result = json_decode($response, true);

        // Extraire le texte généré
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            echo "Texte généré : \n\n";
            echo $result['candidates'][0]['content']['parts'][0]['text'];
        } else {
            echo "Aucune réponse reçue.";
        }
    }
}
