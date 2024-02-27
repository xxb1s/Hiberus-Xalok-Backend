<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class VehiclesController extends AbstractController
{
    #[Route('/vehicles', name: 'app_vehicles')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VehiclesController.php',
        ]);
    }

    #[Route('/vehicles', name: 'create_vehicle', methods: ['POST'])]
    public function create()
    {
        
    }
}
