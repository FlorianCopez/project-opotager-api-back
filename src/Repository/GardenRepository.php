<?php

namespace App\Repository;

use App\Entity\Garden;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Garden>
 *
 * @method Garden|null find($id, $lockMode = null, $lockVersion = null)
 * @method Garden|null findOneBy(array $criteria, array $orderBy = null)
 * @method Garden[]    findAll()
 * @method Garden[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GardenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garden::class);
    }

    public function add(Garden $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Garden $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * get gardens using a town's coordinates
     *
     * @param float $lat city latitude
     * @param float $lon city longitude
     * @param integer $distance maximum distance from the city
     * @return gardens[] array of gardens found
     */
    public function findGardensByCoordinates(float $lat, float $lon, int $dist)
    {
        $formule = "(6366*acos(cos(radians($lat))*cos(radians(`lat`))*cos(radians(`lon`) - radians($lon))+sin(radians($lat))*sin(radians(`lat`))))";

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
                SELECT garden.*, user.username, user.email, user.phone, user.avatar,' . $formule . ' AS dist
                FROM garden
                INNER JOIN user ON garden.user_id = user.id
                WHERE ' . $formule . '<= :dist 
                ORDER BY dist ASC
                ';

        $resultSet = $conn->executeQuery($sql, ['dist' => $dist]);

        return $resultSet->fetchAllAssociative();
    }

    //    /**
    //     * @return Garden[] Returns an array of Garden objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Garden
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
