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
        // Assurez-vous d'avoir configuré l'authentification appropriée pour Cloud Vision.
        // L'approche la plus courante est d'utiliser un jeton d'accès OAuth2.
        // Pour les tests rapides, vous pouvez utiliser une clé API, mais ce n'est PAS recommandé
        // pour la production. Remplacez par votre clé ou votre jeton.
        $apiKey = "AIzaSyBEe6BmgXhQWSY5Jh9JBXX33id44pZM8sM";
        $apiUrl = "https://vision.googleapis.com/v1/images:annotate?key=" . $apiKey;

        // 2. Préparation de l'Image
        // Chemin vers votre image locale
        $imagePath = $request->query->get('filepath');
        if (empty($imagePath))
            return new JsonResponse("empty filepath", 400);

        // Lecture et encodage de l'image en Base64
        if (!file_exists($imagePath)) {
            die("Erreur : Le fichier image n'existe pas à l'emplacement spécifié.");
        }
        $imageData = file_get_contents($imagePath);
        $base64Image = base64_encode($imageData);

        // 3. Catégories Cibles (pour la post-filtration)
        // C'est la liste de catégories que vous souhaitez *vérifier* dans les résultats.
        $categories = $this->em->getRepository(PostPhotoCategory::class)->createQueryBuilder('category')
            ->select('category.name')
            ->getQuery()
            ->getSingleColumnResult();

        dd($categories);

        // 4. Construction de la Requête JSON
        // La fonctionnalité 'LABEL_DETECTION' est utilisée pour la classification/étiquetage.
        // 'maxResults' limite le nombre de labels retournés.
        $requestData = [
            'requests' => [
                [
                    'image' => [
                        'content' => $base64Image // L'image encodée
                    ],
                    'features' => [
                        [
                            'type' => 'LABEL_DETECTION',
                            'maxResults' => 10 // Récupère jusqu'à 10 labels
                        ]
                    ]
                ]
            ]
        ];
        $json_data = json_encode($requestData);

        // 5. Appel cURL
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

        echo "## Résultats de la Classification Cloud Vision :\n";
        echo "Image analysée : **" . basename($imagePath) . "**\n\n";

        if (isset($responseArray['responses'][0]['labelAnnotations'])) {
            $labels = $responseArray['responses'][0]['labelAnnotations'];
            $foundMatches = [];

            echo "### Labels détectés par l'API :\n";
            foreach ($labels as $label) {
                $description = $label['description'];
                // Formatage du score à 2 décimales pour l'affichage
                $score = number_format($label['score'], 2);

                echo "* **" . $description . "** (Confiance: " . $score . ")\n";

                // 7. Post-Filtration selon vos catégories cibles
                // On vérifie si le label détecté est dans notre liste cible (en ignorant la casse pour plus de flexibilité)
                if (in_array(ucfirst(strtolower($description)), array_map('ucfirst', array_map('strtolower', $targetCategories)))) {
                    $foundMatches[] = [
                        'category' => $description,
                        'score' => $score
                    ];
                }
            }

            echo "\n---\n";

            // Affichage des correspondances
            if (!empty($foundMatches)) {
                echo "### ✅ Correspondances trouvées dans votre liste cible (" . count($foundMatches) . ") :\n";
                foreach ($foundMatches as $match) {
                    echo "* Catégorie cible : **" . $match['category'] . "** (Confiance: " . $match['score'] . ")\n";
                }
            } else {
                echo "### ❌ Aucune des catégories cibles n'a été trouvée avec une haute confiance.\n";
            }
        } elseif (isset($responseArray['error']['message'])) {
            echo "## Erreur de l'API Cloud Vision :\n";
            echo $responseArray['error']['message'] . "\n";
        } else {
            echo "## Erreur Inconnue :\n";
            print_r($responseArray);
        }
    }
}
