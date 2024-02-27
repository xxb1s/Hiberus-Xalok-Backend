<?php

namespace App\Repository;

use App\Entity\Drivers;
use App\Entity\Trips;
use App\Entity\Vehicles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
     * @psalm-param array{vehicle: Vehicles, driver: Drivers, date: \DateTimeImmutable} $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $trip = new Trips();
            $trip->setVehicle($data['vehicle']);
            $trip->setDriver($data['driver']);
            $trip->setDate($data['date']);

            $this->getEntityManager()->persist($trip);
            $this->getEntityManager()->flush();

            return [
                'success' => true,
                'msg' => 'ok'
            ];
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage()
            ];
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
