<?php

namespace App\Repository;

use App\Entity\Ad;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Ad>
 */
class AdRepository extends ServiceEntityRepository
{
    public const PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    /**
     * @return Pagerfanta<Ad>
     */
    public function listOrderedPaginated(int $currentPage, int $maxPerPage = self::PER_PAGE, ?User $user = null): Pagerfanta
    {
        $qb = $this->createQueryBuilder('a')->orderBy('a.updatedAt', 'DESC');

        if ($user) {
            $qb->andWhere('a.user = :user')->setParameter('user', $user);
        }

        /** @var Pagerfanta<Ad> $pager */
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(new QueryAdapter($qb), $currentPage, $maxPerPage);

        return $pager;
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
