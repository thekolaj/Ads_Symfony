<?php

namespace App\Repository;

use App\Entity\Ad;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Ad>
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    /**
     * @return Ad[] Returns an array of Ads
     */
    public function findList(?User $user = null): array
    {
        $qb = $this->createQueryBuilder('a')->orderBy('a.updatedAt', 'DESC');

        if ($user) {
            $qb->andWhere('a.user = :user')->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): Ad
    {
        $ad = $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->addSelect('u')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$ad instanceof Ad) {
            throw new NotFoundHttpException("Ad $id not found");
        }

        return $ad;
    }
}
