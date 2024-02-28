<?php

namespace App\Controller;

use App\Entity\Vehicles;
use App\Repository\VehiclesRepository;
use App\Service\VehiclesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
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
    public function index(SerializerInterface $serializer): JsonResponse
    {
        try {
            $vehicles = $this->repository->findAll();

            if (empty($vehicles)) {
                return $this->json([
                    'messages' => 'not found',
                    'vehicles' => []
                ], 404);
            }

            return $this->json([
                'messages' => 'list vehicles',
                'vehicles' => $vehicles
            ], 200);
        } catch (\Exception $exception) {
            return $this->json([
                'messages' => 'server error',
                'vehicles' => []
            ], 500);
        }
    }

    #[Route('/vehicles', name: 'create_vehicle', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $isValid = $this->service->validate($data);
        if (!$isValid['success']) {
            return $this->json([
                'messages' => 'server error',
                'errors' => []
            ], 500);
        }

        if (!$isValid['valid']) {
            return $this->json([
                'messages' => 'invalid fields',
                'errors' => $isValid['msg']
            ], 422);
        }

        $vehicle = $this->repository->create($data);
        if (!$vehicle['success']) {
            return $this->json([
                'messages' => 'server error',
                'errors' => []
            ], 500);
        }
        $vehicle = $vehicle['vehicle'];

        return $this->json([
            'messages' => 'vehicle created',
            'errors' => [],
            'vehicle' => $vehicle
        ], 201);
    }

    #[Route('/vehicles/{id}', name: 'edit_vehicle', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function edit(Vehicles $vehicle, Request $request): JsonResponse
    {
        $data = $request->toArray();

        if ($vehicle->getDeletedAt()) {
            return $this->json([
                'messages' => 'not found',
                'errors' => []
            ], 404);
        }

        $isValid = $this->service->validate($data);

        if (!$isValid['success']) {
            return $this->json([
                'messages' => 'server error',
                'errors' => []
            ], 500);
        }

        if (!$isValid['valid']) {
            return $this->json([
                'messages' => 'invalid fields',
                'errors' => $isValid['msg']
            ], 422);
        }

        $update = $this->repository->update($vehicle, $data);
        if (!$update['success']) {
            return $this->json([
                'messages' => 'server error',
                'errors' => []
            ], 500);
        }
        $vehicle = $update['vehicle'];

        return $this->json([
            'messages' => 'vehicle updated',
            'errors' => [],
            'vehicle' => $vehicle
        ], 200);
    }

    #[Route('/vehicles/{id}', name: 'delete_vehicle', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Vehicles $vehicle): JsonResponse
    {
        if ($vehicle->getDeletedAt()) {
            return $this->json([
                'messages' => 'not found',
                'errors' => []
            ], 404);
        }

        $delete = $this->repository->delete($vehicle);

        if (!$delete['success']) {
            return $this->json([
                'messages' => 'server error'
            ], 500);
        }

        return $this->json([
            'messages' => 'vehicle deleted'
        ], 200);
    }
}
