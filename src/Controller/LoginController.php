<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LoginController extends AbstractController
{

    public function __construct(
        private Security $security,
        private SerializerInterface $serializer,
    ) {}

    #[Route('/user_info', name: 'app_user_info', methods: ['GET'])]
    public function getUserInfo()
    {
        $user = $this->security->getUser();

        $normalizedUser = $this->serializer->normalize($user, null, ['groups' => ['user:collection', 'userRole:collection']]);


        return $this->json($normalizedUser);
    }
}
