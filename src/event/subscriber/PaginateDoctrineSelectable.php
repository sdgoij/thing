<?php
namespace thing\event\subscriber;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use thing\util\DoctrineEntityRepositorySelector;

/**
 * Class PaginateDoctrineSelectable
 * @package thing\event\subscriber
 */
class PaginateDoctrineSelectable implements EventSubscriberInterface {
	/**
	 * @param ItemsEvent $event
	 */
	public function items(ItemsEvent $event) {
		if ($event->target instanceof Selectable) {
			$criteria = $event->target instanceof DoctrineEntityRepositorySelector
				? clone $event->target->getCriteria() : new Criteria();
			$event->count = $this->count($event->target);
			$event->items = $this->getItems($event, $criteria);
			$event->stopPropagation();
		}
	}

	/**
	 * @return array The event names to listen to
	 */
	public static function getSubscribedEvents() {
		return ['knp_pager.items' => ['items', 1]];
	}

	/**
	 * @param ItemsEvent $event
	 * @param Criteria $criteria
	 * @return array
	 */
	public function getItems(ItemsEvent $event, Criteria $criteria) {
		$criteria->setFirstResult($event->getOffset())->setMaxResults($event->getLimit());
		return $event->target->matching($criteria)->toArray();
	}

	/**
	 * @param Selectable $selectable
	 * @return int
	 */
	private function count(Selectable $selectable) {
		return !$selectable instanceof \Countable
			? count($selectable->matching(Criteria::create()))
			: count($selectable);
	}
}
