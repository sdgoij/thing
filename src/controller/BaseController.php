<?php
namespace thing\controller;

use ArrayAccess;
use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Silex\Application\FormTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;

abstract class BaseController implements ArrayAccess {
	use FormTrait, TwigTrait, UrlGeneratorTrait;

	/**
	 * @param Container $container
	 */
	public function __construct(Container $container) {
		$this->container = $container;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager() {
		return $this->container['orm.em'];
	}

	/**
	 * @param mixed $o
	 * @return bool
	 */
	public function offsetExists($o) {
		return $this->container->offsetExists($o);
	}

	/**
	 * @param mixed $o
	 * @return mixed
	 */
	public function offsetGet($o) {
		return $this->container->offsetGet($o);
	}

	/**
	 * @param mixed $o
	 * @param mixed $v
	 */
	public function offsetSet($o, $v) {
		$this->container->offsetSet($o, $v);
	}

	/**
	 * @param mixed $o
	 */
	public function offsetUnset($o) {
		$this->container->offsetUnset($o);
	}

	/** @var Container */
	protected $container;
}
