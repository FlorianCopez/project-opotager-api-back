<?php

namespace App\Controller\Api;

use App\Entity\Garden;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GardenController extends AbstractController
{
    /**
     * @Route("/api/garden", name="app_api_garden")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Api/GardenController.php',
        ]);
    }
}
