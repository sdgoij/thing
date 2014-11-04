<?php
namespace Application\Paginator\Adapter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository as Repository;
use Zend\Paginator\Adapter\AdapterInterface;

class EntityRepository implements AdapterInterface {
    /**
     * @var Criteria $criteria
     */
    private $criteria;
    /**
     * @var Repository $repository
     */
    private $repository;

    /**
     * @var string $id Identifier column name
     */
    private $id = 'id';

    /**
     * @param Repository    $repository
     * @param Criteria|null $criteria
     */
    public function __construct(Repository $repository, Criteria $criteria = null) {
        $this->criteria   = $criteria ? clone $criteria : new Criteria();
        $this->repository = $repository;
    }

    /**
     * @param string $id Set identifier to use in count()
     * @return EntityRepository
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return array
     */
    public function getItems($offset, $itemCountPerPage) {
        $this->criteria->setFirstResult($offset)->setMaxResults($itemCountPerPage);
        return $this->repository->findBy([],
            $this->criteria->getOrderings(),
            $itemCountPerPage, $offset
        );
    }

    /**
     * @return int
     */
    public function count() {
        $criteria = clone $this->criteria;
        $criteria->setFirstResult(null);
        $criteria->setMaxResults(null);
        $criteria->orderBy([]);
        return $this->repository
            ->createQueryBuilder('t0')
            ->select('count(t0.'.$this->id.') as c0')
            ->addCriteria($criteria)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
