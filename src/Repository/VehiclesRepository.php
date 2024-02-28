<?php

namespace App\Repository;

use App\Entity\Vehicles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicles>
 *
 * @method Vehicles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicles[]    findAll()
 * @method Vehicles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiclesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicles::class);
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

            $vehicle->setCreatedAt(new \DateTimeImmutable('now'));

            $this->getEntityManager()->persist($vehicle);
            $this->getEntityManager()->flush();

            return [
                'success' => true,
                'msg' => 'ok',
                'vehicle' => $vehicle
            ];
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
                'vehicle' => null
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
