<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CloudVisionService
{

    public function __construct(
        private ParameterBagInterface $params
    ) {}

    public function getLabels(String $imagePath): array
    {
        $apiKey = $this->params->get('google_api_key');


        // 1. Détection du MimeType (Utilisation de la méthode robuste vue précédemment)
        // ... (Code de détection de mimeType) ...

        if (extension_loaded('fileinfo')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $imagePath);
            finfo_close($finfo);
        } else {
            // ... (Méthode de secours pour mimeType) ...
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $mimeType = 'image/jpeg';
                    break;
                case 'png':
                    $mimeType = 'image/png';
                    break;
                case 'webp':
                    $mimeType = 'image/webp';
                    break;
                default:
                    // Assurez-vous que l'API Gemini supporte ce type. 
                    // JPEG est souvent un bon choix par défaut.
                    $mimeType = 'image/jpeg';
                    echo "Avertissement : Type MIME non standard ou inconnu. Utilisation par défaut : image/jpeg\n";
            }
        }

        if (strpos($mimeType, 'image/') !== 0) {
            throw new Exception("Le fichier spécifié n'est pas une image reconnue.");
        }

        // 2. Préparation
        $apiUrl = "https://vision.googleapis.com/v1/images:annotate?key=" . $apiKey;
        $base64Image = base64_encode(file_get_contents($imagePath));

        $requestData = [
            'requests' => [
                [
                    'image' => ['content' => $base64Image],
                    'features' => [
                        [
                            'type' => 'LABEL_DETECTION',
                            'maxResults' => 10 // Limite à 10 classifications
                        ]
                    ]
                ]
            ]
        ];

        // 3. Appel cURL et décodage
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        $response = curl_exec($ch);
        curl_close($ch);

        $responseArray = json_decode($response, true);

        if (isset($responseArray['responses'][0]['labelAnnotations'])) {
            $labels = [];
            // On ne retourne que les descriptions (labels bruts en anglais)
            foreach ($responseArray['responses'][0]['labelAnnotations'] as $label) {
                $labels[] = $label['description'];
            }
        }

        // Gestion des erreurs de l'API Cloud Vision
        if (isset($responseArray['error']['message'])) {
            throw new Exception("Erreur Cloud Vision: " . $responseArray['error']['message']);
        }

        return []; // Retourne un tableau vide si aucun label n'est trouvé
    }
}
