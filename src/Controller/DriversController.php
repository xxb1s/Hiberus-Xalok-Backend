<?php

namespace App\Controller;

use App\Entity\Drivers;
use App\Entity\Vehicles;
use App\Repository\DriversRepository;
use App\Service\DriversService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class DriversController extends AbstractController
{
    private DriversRepository $repository;
    private DriversService $service;

    public function __construct(DriversRepository $repository, DriversService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    #[Route('/drivers', name: 'list_drivers', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {
            $drivers = $this->repository->findAll();

            if (empty($drivers)) {
                return $this->json([
                    'messages' => 'not found',
                    'drivers' => []
                ], 404);
            }

            return $this->json([
                'messages' => 'list drivers',
                'drivers' => $drivers
            ], 200);
        } catch (Exception $exception) {
            return $this->json([
                'messages' => 'server error',
                'drivers' => []
            ], 500);
        }
    }

    #[Route('/drivers', name: 'create_driver', methods: ['POST'])]
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

        $driver = $this->repository->create($data);
        if (!$driver['success']) {
            return $this->json([
                'messages' => 'server error',
                'errors' => []
            ], 500);
        }
        $driver = $driver['driver'];

        return $this->json([
            'messages' => 'driver created',
            'errors' => [],
            'driver' => $driver
        ], 201);
    }

    #[Route('/drivers/{id}', name: 'edit_driver', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function edit(Drivers $driver, Request $request): JsonResponse
    {
        $data = $request->toArray();

        if ($driver->getDeletedAt()) {
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

        $update = $this->repository->update($driver, $data);
        if (!$update['success']) {
            return $this->json([
                'messages' => 'server error',
                'errors' => []
            ], 500);
        }
        $driver = $update['driver'];

        return $this->json([
            'messages' => 'driver updated',
            'errors' => [],
            'driver' => $driver
        ], 200);
    }

    #[Route('/drivers/{id}', name: 'delete_driver', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Drivers $driver): JsonResponse
    {
        if ($driver->getDeletedAt()) {
            return $this->json([
                'messages' => 'not found',
                'errors' => []
            ], 404);
        }

        $delete = $this->repository->delete($driver);

        if (!$delete['success']) {
            return $this->json([
                'messages' => 'server error'
            ], 500);
        }

        return $this->json([
            'messages' => 'driver deleted'
        ], 200);
    }
}
