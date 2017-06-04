<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait SearchOrderJoinPaged
 * Requires repository to own "TableName" trait.
 * @author  Matthias Morin <tangoman@free.fr>
 *
 * @package AppBundle\Repository\Traits
 */
Trait SearchOrderJoinPaged
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function searchOrderJoinPaged(ParameterBag $query)
    {
        // Sets default values
        $page = $query->get('page', 1);
        $limit = $query->get('limit', 10);

        if (!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if (!is_numeric($limit)) {
            throw new \InvalidArgumentException(
                '$limit must be an integer ('.gettype($limit).' : '.$limit.')'
            );
        }

        // QueryBuilder
        $dql = $this->createQueryBuilder($this->getTableName());
        // Search
        $dql = $this->search($dql, $query);
        // Order
        $dql = $this->order($dql, $query);
        // Joins User
        $dql->leftJoin($this->getTableName().'.user', 'user');
        // Group
        $dql->groupBy($this->getTableName().'.id');

        $firstResult = ($page - 1) * $limit;
        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($limit);
        $paginator = new Paginator($query);

        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }
}