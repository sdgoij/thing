<?php
namespace Application\Factory;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthService implements FactoryInterface {
    public function createService(ServiceLocatorInterface $locator) {
        return new AuthenticationService(null, new \Application\Auth\Adapter(
            $locator->get('Doctrine\ORM\EntityManager')));
    }
}
