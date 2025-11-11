<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: UserListener::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: UserListener::class)]
class UserListener
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function prePersist(User $user, PrePersistEventArgs $args)
    {
        $this->handlePasswordHashing($user);
    }

    public function preUpdate(User $user, PreUpdateEventArgs $args)
    {
        if (!empty($args->getEntityChangeSet()["password"])) {
            $this->handlePasswordHashing($user);
        }
    }

    private function handlePasswordHashing(User $user)
    {
        $plainPassword = $user->getPassword();
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }
    }
}
