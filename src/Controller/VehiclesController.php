<?php

namespace App\Controller;

use App\Repository\VehiclesRepository;
use App\Service\VehiclesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VehiclesController extends AbstractController
{
    private VehiclesService $service;
    private VehiclesRepository $repository;

    public function __construct(VehiclesService $service, VehiclesRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    #[Route('/vehicles', name: 'list_vehicles', methods: ['GET'])]
    public function index(): JsonResponse
    {

    }

    #[Route('/vehicles', name: 'create_vehicle', methods: ['POST'])]
    public function create(ValidatorInterface $validator, Request $request): JsonResponse
    {
        $data = $request->toArray();
        $isValid = $this->service->validate($validator, $data);
        if (!$isValid['success']) {
            return $this->json([
                'messages' => 'Server Error',
                'errors' => []
            ], 500);
        }

        if (!$isValid['valid']) {
            return $this->json([
                'messages' => 'Invalid Fields',
                'errors' => $isValid['msg']
            ], 422);
        }

        $vehicle = $this->repository->create($data);
        if (!$vehicle['success']) {
            return $this->json([
                'messages' => 'Server Error',
                'errors' => []
            ], 500);
        }
        $vehicle = $vehicle['vehicle'];

        return $this->json([
            'messages' => 'Vehicle Created',
            'errors' => [],
            'vehicle' => $vehicle->toArray()
        ]);
    }
}
