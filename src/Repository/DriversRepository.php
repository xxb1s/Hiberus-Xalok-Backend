<?php

namespace App\Repository;

use App\Entity\Drivers;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Drivers>
 *
 * @method Drivers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Drivers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Drivers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriversRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Drivers::class);
    }

    public function findAll(bool $trash = false): array
    {
        $qb = $this->createQueryBuilder('d');

        if (!$trash) {
            $qb->where('d.deleted_at IS NULL');
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param array $data
     * @psalm-param array{name: string, surname: string, license: string} $data
     * @return array
     * @psalm-return array{success: bool, msg: string, driver: Drivers|null}
     */
    public function create(array $data): array
    {
        try {
            $driver = new Drivers();
            $driver->setName($data['name']);
            $driver->setSurname($data['surname']);
            $driver->setLicense($data['license']);

            $driver->setCreatedAt(new DateTimeImmutable('now'));

            $this->getEntityManager()->persist($driver);
            $this->getEntityManager()->flush();

            return [
                'success' => true,
                'msg' => 'ok',
                'driver' => $driver
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
                'driver' => null
            ];
        }
    }

    /**
     * @param Drivers $driver
     * @param array $data
     * @psalm-param array{name: string, surname: string, license: string} $data
     * @return array
     * @psalm-return array{success: bool, msg: string, driver: Drivers|null}
     */
    public function update(Drivers $driver, array $data): array
    {
        try {
            $driver->setName($data['name']);
            $driver->setSurname($data['surname']);
            $driver->setLicense($data['license']);

            $driver->setUpdatedAt(new DateTimeImmutable('now'));

            $this->getEntityManager()->flush();

            return [
                'success' => true,
                'msg' => 'ok',
                'driver' => $driver
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
                'driver' => null
            ];
        }
    }

    /**
     * @param Drivers $driver
     * @return array
     * @psalm-return array{success: bool, msg: string}
     */
    public function delete(Drivers $driver): array
    {
        try {
            $driver->setDeletedAt(new DateTimeImmutable('now'));

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
