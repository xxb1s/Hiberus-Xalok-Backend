<?php

namespace App\Repository;

use App\Entity\Drivers;
use App\Entity\Trips;
use App\Entity\Vehicles;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Trips>
 *
 * @method Trips|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trips|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trips[]    findAll()
 * @method Trips[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trips::class);
    }

    /**
     * @param array $data
     * @psalm-param array{vehicle: Vehicles, driver: Drivers, date: DateTimeImmutable} $data
     * @return array
     * @psalm-return array{success: bool, msg: string, trip: Trips|null}
     */
    public function create(array $data): array
    {
        try {
            $trip = new Trips();
            $trip->setVehicle($data['vehicle']);
            $trip->setDriver($data['driver']);
            $trip->setDate($data['date']);

            $trip->setCreatedAt(new DateTimeImmutable('now'));

            $this->getEntityManager()->persist($trip);
            $this->getEntityManager()->flush();

            return [
                'success' => true,
                'msg' => 'ok',
                'trip' => $trip
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
                'trip' => null
            ];
        }
    }

    /**
     * @param Vehicles $vehicle
     * @param DateTimeImmutable $date
     * @return bool|null
     */
    public function hasVehicleTripOnDate(Vehicles $vehicle, DateTimeImmutable $date): ?bool
    {
        try {
            $qb = $this->createQueryBuilder('t');

            $qb->where('t.vehicle = :vehicle')
                ->andWhere('t.date = :date')
                ->setParameters(new ArrayCollection([
                    new Parameter('vehicle', $vehicle),
                    new Parameter('date', $date)
                ]))
                ->setMaxResults(1);

            $result = $qb->getQuery()->execute();

            return !empty($result);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param Drivers $driver
     * @param DateTimeImmutable $date
     * @return bool|null
     */
    public function hasDriverTripOnDate(Drivers $driver, DateTimeImmutable $date): ?bool
    {
        try {
            $qb = $this->createQueryBuilder('t');

            $qb->where('t.driver = :driver')
                ->andWhere('t.date = :date')
                ->setParameters(new ArrayCollection([
                    new Parameter('driver', $driver),
                    new Parameter('date', $date)
                ]))
                ->setMaxResults(1);

            $result = $qb->getQuery()->execute();

            return !empty($result);
        } catch (Exception $exception) {
            return null;
        }
    }

    //    /**
    //     * @return Trips[] Returns an array of Trips objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Trips
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
