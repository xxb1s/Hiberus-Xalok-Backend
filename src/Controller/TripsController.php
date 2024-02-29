<?php

namespace App\Controller;

use App\Repository\TripsRepository;
use App\Service\TripsService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TripsController extends AbstractController
{
    private TripsRepository $repository;
    private TripsService $service;

    public function __construct(TripsRepository $repository, TripsService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    #[Route('/trips', name: 'list_trips', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {
            $trips = $this->repository->findAll();

            if (empty($trips)) {
                return $this->json([
                    'messages' => 'not found',
                    'trips' => []
                ], 404);
            }

            return $this->json([
                'messages' => 'list trips',
                'trips' => $trips
            ], 200, [], ['groups' => ['list_trips', 'list_vehicles', 'list_drivers']]);
        } catch (\Exception $exception) {
            return $this->json([
                'messages' => 'server error',
                'trips' => []
            ], 500);
        }
    }

    #[Route('/trips', name: 'create_trip', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $data['date'] = new DateTimeImmutable($data['date']);
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

        $data['vehicle'] = $isValid['vehicle'];
        $data['driver'] = $isValid['driver'];
        $trip = $this->repository->create($data);
        if (!$trip['success']) {
            return $this->json([
                'messages' => 'server error',
                'errors' => []
            ], 500);
        }
        $trip = $trip['trip'];

        return $this->json([
            'messages' => 'trip created',
            'errors' => [],
            'trip' => [
                'date' => $trip->getDate(),
                'vehicle' => $trip->getVehicle()->getId(),
                'driver' => $trip->getDriver()->getId()
            ]
        ], 201);
    }
}
