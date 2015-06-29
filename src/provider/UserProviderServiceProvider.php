<?php
namespace thing\provider;

use Doctrine\ORM\EntityRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use thing\entity\User;
use thing\util\UserWrapper;

class UserProviderServiceProvider implements UserProviderInterface, ServiceProviderInterface {
	/** @var Container */
	private $container;

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Container $app
	 */
	public function register(Container $app) {
		$this->container = $app;
		$app['users'] = $this;
	}

	/**
	 * Loads the user for the given username.
	 *
	 * This method must throw UsernameNotFoundException if the user is not
	 * found.
	 *
	 * @param string $username The username
	 * @return UserInterface
	 * @see UsernameNotFoundException
	 * @throws UsernameNotFoundException if the user is not found
	 */
	public function loadUserByUsername($username) {
		/** @var EntityRepository $users */
		$users = $this->container['orm.em']->getRepository(User::class);
		if ($user = $users->findOneBy(['username' => $username])) {
			/** @var User $user */
			return new UserWrapper($user);
		}
		$e = new UsernameNotFoundException();
		$e->setUsername($username);
		throw $e;
	}

	/**
	 * Refreshes the user for the account interface.
	 *
	 * It is up to the implementation to decide if the user data should be
	 * totally reloaded (e.g. from the database), or if the UserInterface
	 * object can just be merged into some internal array of users / identity
	 * map.
	 *
	 * @param UserInterface $user
	 * @return UserInterface
	 * @throws UnsupportedUserException if the account is not supported
	 */
	public function refreshUser(UserInterface $user) {
		try {
			return $this->loadUserByUsername($user->getUsername());
		} catch (UsernameNotFoundException $e) {
			throw new UnsupportedUserException($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Whether this provider supports the given user class.
	 *
	 * @param string $class
	 * @return bool
	 */
	public function supportsClass($class) {
		return $class === UserWrapper::class;
	}
}
