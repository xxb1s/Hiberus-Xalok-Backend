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
     */
    public function create(array $data): array
    {
        try {
            $vehicle = new Vehicles();
            $vehicle->setBrand($data['brand']);
            $vehicle->setModel($data['model']);
            $vehicle->setPlate($data['plate']);
            $vehicle->setLicenseRequired($data['license']);

            $this->getEntityManager()->persist($vehicle);
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
