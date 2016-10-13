<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentRepository extends EntityRepository
{
    /**
     * Comments pagination.
     *
     * @param int $post Post id
     * @param int $page
     * @param int $max
     * @return Paginator
     */
    public function findAllPaged($post, $page = 1, $max = 10)
    {
        if( !is_numeric($page) ) {

            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if( !is_numeric($page) ) {

            throw new \InvalidArgumentException(
                '$max must be an integer ('.gettype($max).' : '.$max.')'
            );
        }

        // Queries post comments
        $dql = $this->createQueryBuilder('comment');
        $dql->andWhere('comment.post = :post');
        $dql->setParameter(':post', $post);
        $dql->orderBy('comment.dateCreated', 'DESC');

        $firstResult = ($page - 1) * $max;
        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);
        $paginator = new Paginator($query);

        if( ($paginator->count() <= $firstResult) && $page != 1 ) {

            throw new NotFoundHttpException('Page not found');

        }

        return $paginator;
    }

}
