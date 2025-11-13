<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostPhoto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/normal_api/posts')]
class PostController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private SerializerInterface $serializer,
        private SluggerInterface $slugger
    ) {}

    #[Route('/save_post_image', methods: ['POST'])]
    public function savePostImage(Request $request)
    {
        // Récupérer le fichier
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            return $this->json(['error' => 'Fichier manquant'], Response::HTTP_BAD_REQUEST);
        }

        // Générer un nom de fichier unique
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = "Post" . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        // Déplacer le fichier dans le dossier uploads
        $uploadDir = $this->getParameter('post_uploads_directory'); // définir dans services.yaml ou .env
        $uploadedFile->move($uploadDir, $newFilename);

        return new JsonResponse(['filename' => $newFilename]);
    }
}
