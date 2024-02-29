<?php

namespace App\Repository;

use App\Entity\Vehicles;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Vehicles>
 *
 * @method Vehicles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiclesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicles::class);
    }

    /**
     * @param bool $trash
     * @return array
     */
    public function findAll(bool $trash = false): array
    {
        $qb = $this->createQueryBuilder('v');

        if (!$trash) {
            $qb->where('v.deleted_at IS NULL');
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param array $data
     * @psalm-param array{brand: string, model: string, plate: string, license: string} $data
     * @return array
     * @psalm-return array{success: bool, msg: string, vehicle: Vehicles|null}
     */
    public function create(array $data): array
    {
        try {
            $vehicle = new Vehicles();
            $vehicle->setBrand($data['brand']);
            $vehicle->setModel($data['model']);
            $vehicle->setPlate($data['plate']);
            $vehicle->setLicense($data['license']);

            $vehicle->setCreatedAt(new DateTimeImmutable('now'));

            $this->getEntityManager()->persist($vehicle);
            $this->getEntityManager()->flush();

            return [
                'success' => true,
                'msg' => 'ok',
                'vehicle' => $vehicle
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
                'vehicle' => null
            ];
        }
    }

    /**
     * @param Vehicles $vehicle
     * @param array $data
     * @psalm-param array{brand: string, model: string, plate: string, license: string} $data
     * @return array
     * @psalm-return array{success: bool, msg: string, vehicle: Vehicles|null}
     */
    public function update(Vehicles $vehicle, array $data): array
    {
        try {
            $vehicle->setBrand($data['brand']);
            $vehicle->setModel($data['model']);
            $vehicle->setPlate($data['plate']);
            $vehicle->setLicense($data['license']);

            $vehicle->setUpdatedAt(new DateTimeImmutable('now'));

            $this->getEntityManager()->flush();

            return [
                'success' => true,
                'msg' => 'ok',
                'vehicle' => $vehicle
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
                'vehicle' => null
            ];
        }
    }

    /**
     * @param Vehicles $vehicle
     * @return array
     * @psalm-return array{success: bool, msg: string}
     */
    public function delete(Vehicles $vehicle): array
    {
        try {
            $vehicle->setDeletedAt(new DateTimeImmutable('now'));

            $this->getEntityManager()->flush();

            return [
                'success' => true,
                'msg' => 'ok'
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
            ];
        }
    }

    public function availableVehiclesByDate(DateTimeImmutable $dateTime): array
    {
        try {
            $qb = $this->createQueryBuilder('v');

            $qb->select('v')
                ->leftJoin('v.trips', 't', 'v.id = t.vehicle_id')
                ->where('t.date <> :date')
                ->orWhere('t.date IS NULL')
                ->setParameter('date', $dateTime);

            $vehicles = $qb->getQuery()->execute();
            return [
                'success' => true,
                'vehicles' => $vehicles
            ];
        } catch (Exception $exception) {
            dd($exception);
            return [
                'success' => false,
                'vehicles' => []
            ];
        }
    }
    //    /**
    //     * @return Vehicles[] Returns an array of Vehicles objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Vehicles
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
