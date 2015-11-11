<?php
namespace Company\YourCompanyBundle\Repository;

use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/*
 * @author: Marco A. Simão
 *
 */
class DoctrinePaginator
{
    protected $offset = 0;
    protected $max = 10;
    protected $queryBuilder;
    protected $page = 1;
    protected $queryCount;
    protected $count = null;
    protected $pageless = false;

    public function __construct($queryBuilder, $queryCount = '')
    {
        $this->queryCount   = $queryCount;
        $this->queryBuilder = $queryBuilder;
    }

    public function getNbResults()
    {
        if (is_numeric($this->count))
            return $this->count;

        if ($this->queryBuilder instanceof Query)
        {
            $this->queryBuilder->setFirstResult($this->offset)
            ->setMaxResults($this->max); //->setHydrationMode(Query::HYDRATE_ARRAY);

            //for now we assume always fetchJoinCollection is true
            $p = new Paginator($this->queryBuilder, true);

            $this->count = $p->count();
        }
        else
        {
            $qb_clone = clone $this->queryBuilder; //remove original order by without affecting query
            $qb = $qb_clone->resetQueryPart('orderBy')->select($this->queryCount)->setMaxResults(1);

            $this->count = (int) $qb->execute()->fetchColumn();
        }

        return $this->count;
    }

    public function setMax($max)
    {
        $this->max = $max;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getMax()
    {
        return $this->max;
    }
    
    public function getOffset($offset)
    {
        return $this->offset;
    }
    
    public function setPage($page)
    {
        $this->page = $page;
    
        // that means page 1 * 10 = 11?
        if ($page > 1)
        {
            $this->offset = ($page - 1) * $this->max;
        }
    }
    
    public function getPages()
    {
        return ceil($this->getNbResults() / $this->max);
    }

    public function getResults()
    {
        if ($this->queryBuilder instanceof Query)
        {
            $this->queryBuilder->setFirstResult($this->offset);

            if ($this->pageless !== true)
                $this->queryBuilder->setMaxResults($this->max); 

            //->setHydrationMode(Query::HYDRATE_ARRAY);

            //for now we assume always fetchJoinCollection is true
            $p = new Paginator($this->queryBuilder, true);

            return $p->getIterator();
        }
        else
        {
            $qb = clone $this->queryBuilder;

            return $this->pageless === true ? $qb->execute()->fetchAll() : $qb->setMaxResults($this->max)->setFirstResult($this->offset)->execute()->fetchAll();
        }
    }

    public function getAllResults()
    {
      if ($this->queryBuilder instanceof Query)
      {
        $this->queryBuilder->setFirstResult($this->offset);
        //for now we assume always fetchJoinCollection is true
        $p = new Paginator($this->queryBuilder, true);

        return $p->getIterator();
      }
      else
      {
        $qb = clone $this->queryBuilder;

        return $qb->execute()->fetchAll();
      }
    }
}
