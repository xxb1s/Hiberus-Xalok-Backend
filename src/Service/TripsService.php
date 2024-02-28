<?php

namespace App\Service;

use App\Entity\Drivers;
use App\Entity\Vehicles;
use App\Repository\DriversRepository;
use App\Repository\TripsRepository;
use App\Repository\VehiclesRepository;
use Exception;
use RuntimeException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\ValidatorBuilder;

class TripsService
{
    private TripsRepository $repository;
    private VehiclesRepository $vehiclesRepository;
    private DriversRepository $driversRepository;

    public function __construct(TripsRepository $repository, VehiclesRepository $vehiclesRepository, DriversRepository $driversRepository)
    {
        $this->repository = $repository;
        $this->vehiclesRepository = $vehiclesRepository;
        $this->driversRepository = $driversRepository;
    }

    /**
     * TODO: Esta función está muy grande para mi gusto|This function is too large for my taste
     * @param array $data
     * @psalm-param array{date: string, vehicle: int, driver: int} $data
     * @return array
     * @psalm-return array{success: bool, valid: bool, msg:string|array, vehicle: Vehicles|null, driver: Drivers|null}
     */
    public function validate(array $data): array
    {
        try {
            $validations = new Assert\Collection([
                'date' => new Assert\Sequentially([
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual('today')
                ]),
                'vehicle' => new Assert\Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Positive()
                ]),
                'driver' => new Assert\Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Positive()
                ])
            ]);

            $validator = new ValidatorBuilder();
            $violations = $validator->getValidator()->validate($data, $validations);

            if ($violations->count() > 0) {
                return [
                    'success' => true,
                    'valid' => false,
                    'msg' => $this->buildErrors($violations),
                    'vehicle' => null,
                    'driver' => null
                ];
            }

            $errors = [];

            $vehicle = $this->vehiclesRepository->findBy(['id' => $data['vehicle'], 'deleted_at' => null]);
            if (!$vehicle) {
                $errors['vehicle'] = 'Vehicle not found';
            }
            $vehicle = $vehicle[0];

            $driver = $this->driversRepository->findBy(['id' => $data['driver'], 'deleted_at' => null]);
            if (!$driver) {
                $errors['driver'] = 'Driver not found';
            }
            $driver = $driver[0];

            if (count($errors) > 0) {
                return [
                    'success' => true,
                    'valid' => false,
                    'msg' => $errors,
                    'vehicle' => null,
                    'driver' => null
                ];
            }
            $vehicleInvalid = $this->repository->hasVehicleTripOnDate($vehicle, $data['date']);
            if (is_null($vehicleInvalid)) {
                throw new RuntimeException('hasVehicleTripOnDate Error');
            }
            if ($vehicleInvalid) {
                return [
                    'success' => true,
                    'valid' => false,
                    'msg' => [
                        'vehicle' => 'Vehicle is busy'
                    ],
                    'vehicle' => null,
                    'driver' => null
                ];
            }

            $driverInvalid = $this->repository->hasDriverTripOnDate($driver, $data['date']);
            if (is_null($driverInvalid)) {
                throw new RuntimeException('hasDriverTripOnDate Error');
            }
            if ($driverInvalid) {
                return [
                    'success' => true,
                    'valid' => false,
                    'msg' => [
                        'driver' => 'Driver is busy'
                    ],
                    'vehicle' => null,
                    'driver' => null
                ];
            }

            return [
                'success' => true,
                'valid' => true,
                'msg' => [],
                'vehicle' => $vehicle,
                'driver' => $driver
            ];
        } catch (Exception|RuntimeException $exception) {
            return [
                'success' => false,
                'valid' => false,
                'msg' => [],
                'vehicle' => null,
                'driver' => null
            ];
        }
    }

    public function buildErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[str_replace(['[', ']'],  '', $violation->getPropertyPath())] = $violation->getMessage();
        }

        return $errors;
    }
}