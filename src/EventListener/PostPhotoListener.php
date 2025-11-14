<?php

namespace App\EventListener;

use App\Entity\PostPhoto;
use App\Service\ClassificationService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: PostPhoto::class)]
class PostPhotoListener
{

    public function __construct(
        private ClassificationService $classificationService,
        private ParameterBagInterface $params
    ) {}

    public function postPersist(PostPhoto $photo, PostPersistEventArgs $args)
    {
        // Chemin réel de l'image sur le système de fichiers
        $imagePath = $this->params->get('post_uploads_directory') . "/" . $photo->getFilename();

        $this->classificationService->classifyAndLinkPhoto($photo, $imagePath);
    }
}
