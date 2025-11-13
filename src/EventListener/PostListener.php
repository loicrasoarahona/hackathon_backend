<?php

namespace App\EventListener;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Post::class)]
class PostListener
{

    public function __construct(private Security $security) {}

    public function prePersist(Post $post, PrePersistEventArgs $args)
    {

        // set user
        $user = $this->security->getUser();

        $post->setUser($user);
    }
}
