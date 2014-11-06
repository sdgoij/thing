<?php
namespace thing\factory;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthService implements FactoryInterface {
	public function createService(ServiceLocatorInterface $locator) {
		return new AuthenticationService(null, new \thing\authz\AuthAdapter(
				$locator->get('Doctrine\ORM\EntityManager')
			)
		);
	}
}
