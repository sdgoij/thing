<?php
namespace thing\provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\SecurityServiceProvider as SilexSecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

class SecurityServiceProvider implements ServiceProviderInterface {
	/**
	 * @param Container $app
	 */
	public function register(Container $app) {
		if (!isset($app['session'])) {
			$app->register(new SessionServiceProvider());
		}
		if (!isset($app['security'])) {
			$app->register(new SilexSecurityServiceProvider());
		}
		if (!isset($app['users'])) {
			$app->register(new UserProviderServiceProvider());
		}
		$app['security.encoder.digest'] = function() {
			return new BCryptPasswordEncoder(10);
		};

		$app['security.firewalls'] = [
			'login' => [
				'pattern' => '^.*$',
				'form' => ['login_path' => '/login', 'check_path' => '/login/check'],
				'logout' => ['logout_path' => '/logout'],
				'users' => $app['users'],
				'anonymous' => true,
			],
		];

		$app['security.access_rules'] = [
			['^/(?:submit|reply)', 'ROLE_POSTER'],
		];
	}
}
