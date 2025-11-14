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
}
