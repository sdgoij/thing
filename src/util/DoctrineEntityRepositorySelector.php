<?php
namespace thing\util;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityRepository;

class DoctrineEntityRepositorySelector implements \Countable, Selectable {
	/** @var Criteria */
	private $criteria;

	/** @var EntityRepository */
	private $repository;

	/**
	 * @param EntityRepository $repository
	 * @param Criteria $criteria
	 */
	public function __construct(EntityRepository $repository, Criteria $criteria = null) {
		$this->criteria = $criteria ? clone $criteria : new Criteria();
		$this->repository = $repository;
	}

	/**
	 * @return Criteria
	 */
	public function getCriteria() {
		return $this->criteria;
	}

	/**
	 * @param Criteria $criteria
	 * @return Collection
	 */
	public function matching(Criteria $criteria) {
		return $this->repository->matching($criteria);
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
			->select('count(t0.id) as c0')
			->addCriteria($criteria)
			->getQuery()
			->getSingleScalarResult();
	}
}
