<?php

namespace App\Repository;

use App\Entity\Ad;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public const PER_PAGE = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return Pagerfanta<Comment>
     */
    public function listByAdOrderedPaginated(Ad $ad, int $currentPage, int $maxPerPage = self::PER_PAGE): Pagerfanta
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.user', 'u')
            ->addSelect('u')
            ->andWhere('c.ad = :ad')
            ->setParameter('ad', $ad)
            ->orderBy('c.createdAt', 'DESC');

        /** @var Pagerfanta<Comment> $pager */
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(new QueryAdapter($qb), $currentPage, $maxPerPage);

        return $pager;
    }
}
