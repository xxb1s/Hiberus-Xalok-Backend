<?php

namespace App\Repository;

use App\Entity\Drivers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Drivers>
 *
 * @method Drivers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Drivers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Drivers[]    findAll()
 * @method Drivers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriversRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Drivers::class);
    }

    /**
     * @param array $data
     * @psalm-param array{name: string, surname: string, license: string} $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $driver = new Drivers();
            $driver->setName($data['name']);
            $driver->setSurname($data['surname']);
            $driver->setLicense($data['license']);

            $this->getEntityManager()->persist($driver);
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
    //     * @return Drivers[] Returns an array of Drivers objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Drivers
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
