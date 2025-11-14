<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestController extends AbstractController
{

    public function __construct() {}

    #[Route('/decode', methods: ['GET'])]
    public function decode()
    {
        $text = "```json\n[\n  {\n    \"en\": \"Coast\",\n    \"fr\": \"Côte\"\n  }\n]\n```";
        $clean = preg_replace('/```json|```/', '', $text);
        $clean = trim($clean);

        dd(json_decode($clean, true));
    }
}
