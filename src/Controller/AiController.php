<?php

namespace App\Controller;

use App\Entity\PostPhotoCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use OpenAI\Client;
use OpenAI\Contracts\TransporterContract;
use Symfony\Component\Config\Builder\Method;
use Symfony\Component\HttpFoundation\Request;

#[Route('/ai')]
class AiController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {}

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
        $apiKey = "AIzaSyBEe6BmgXhQWSY5Jh9JBXX33id44pZM8sM";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=$apiKey";

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

        return new JsonResponse($result);

        // Extraire le texte généré
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            echo "Texte généré : \n\n";
            echo $result['candidates'][0]['content']['parts'][0]['text'];
        } else {
            echo "Aucune réponse reçue.";
        }
    }

    #[Route('/classify_image', methods: ['GET'])]
    public function classifyImage(Request $request)
    {
        // 1. Configuration
        $apiKey = $this->getParameter('google_api_key'); // Utilisez votre clé Gemini ici
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        // 2. Préparation de l'Image
        $filename = $request->query->get('filename');
        if (empty($filename))
            return new JsonResponse("Empty filename", 400);
        $imagePath = $this->getParameter('post_uploads_directory') . "/" . $filename;
        if (!file_exists($imagePath)) {
            die("Erreur : Le fichier image n'existe pas.");
        }
        $base64Image = base64_encode(file_get_contents($imagePath));
        $mimeType = 'image/jpeg'; // Assurez-vous que le type MIME correspond à votre image

        // 3. Vos Catégories Cibles Françaises
        $targetCategories = $this->em->getRepository(PostPhotoCategory::class)->createQueryBuilder('category')
            ->select('category.name')
            ->getQuery()
            ->getSingleColumnResult();

        // Création du prompt avec les instructions précises
        $targetList = implode(", ", $targetCategories);
        $prompt = "Voici une image. Veuillez choisir **uniquement** les catégories de la liste suivante qui décrivent le mieux ce que vous voyez, en vous basant sur votre confiance (Ne répondez qu'avec la ou les catégories, séparées par des virgules, sans autre texte ou explication) : " . $targetList;

        // 4. Construction de la Requête JSON (Multimodale)
        $requestData = [
            'contents' => [
                [
                    'parts' => [
                        // Partie 1: L'Image
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $base64Image
                            ]
                        ],
                        // Partie 2: Le Texte (Instructions + Catégories)
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ],
            // Configuration pour une réponse courte et précise
            'generationConfig' => [
                'temperature' => 0.0,
                'maxOutputTokens' => 100
            ]
        ];
        $json_data = json_encode($requestData);

        // 5. Appel cURL (Identique à l'API Texte)
        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Erreur cURL : ' . curl_error($ch);
            exit;
        }

        curl_close($ch);

        // 6. Traitement de la Réponse
        $responseArray = json_decode($response, true);

        echo "## Classification Multimodale avec Gemini :\n";
        echo "Liste de catégories cibles (Français) : **" . $targetList . "**\n\n";


        if (isset($responseArray['candidates'][0]['content']['parts'][0]['text'])) {
            $generatedText = trim($responseArray['candidates'][0]['content']['parts'][0]['text']);

            echo "### ✅ Catégories correspondantes choisies par l'IA :\n";

            // Le texte retourné devrait être une liste de catégories séparées par des virgules
            $results = array_map('trim', explode(',', $generatedText));

            foreach ($results as $result) {
                if (!empty($result)) {
                    echo "* **" . $result . "**\n";
                }
            }
        } elseif (isset($responseArray['error']['message'])) {
            echo "## Erreur de l'API Gemini :\n";
            echo $responseArray['error']['message'] . "\n";
        } else {
            echo "## Erreur Inconnue (Réponse complète) :\n";
            dd($responseArray);
            print_r($responseArray);
        }
    }
}
