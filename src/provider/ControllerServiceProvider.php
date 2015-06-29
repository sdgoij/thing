<?php
namespace thing\provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\Provider\ServiceControllerServiceProvider;
use thing\controller\CommentController;
use thing\controller\PostController;
use thing\controller\UserController;

/**
 * Registers controllers as services and maps routes to controller actions.
 *
 * @package thing\provider
 */
class ControllerServiceProvider implements ServiceProviderInterface, ControllerProviderInterface {
	/**
	 * Returns routes to connect to the given application.
	 *
	 * @param Application $app An Application instance
	 * @return ControllerCollection
	 */
	public function connect(Application $app) {
		$app->register(new ServiceControllerServiceProvider());

		/** @var ControllerCollection $controllers */
		$controllers = $app['controllers_factory'];
		$controllers->get('/{page}', 'controller.post:index')->value('page', 1)->assert('page', '\d+')->bind('home');
		$controllers->get('/discussion/{id}', 'controller.comment:discussion')->assert('id', '\d+')->bind('discussion');
		$controllers->get('/login', 'controller.user:login')->bind('login');
		$controllers->get('/register', 'controller.user:register')->bind('register');
		$controllers->get('/reply/{id}/{parent}', 'controller.comment:reply')
			->assert('parent', '\d+')->value('parent', null)
			->assert('id', '\d+')
			->bind('reply');
		$controllers->get('/submit', 'controller.post:submit')->bind('submit');
		$controllers->get('/r/{id}', 'controller.post:r')->assert('id', '\d+')->bind('redirect');

		$controllers->post('/submit', 'controller.post:submit');
		$controllers->post('/register', 'controller.user:register');
		$controllers->post('/reply', 'controller.comment:reply')->bind('post_reply');

		return $controllers;
	}

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Container $app
	 */
	public function register(Container $app) {
		$app['controller.comment'] = function() use($app) {
			return new CommentController($app);
		};
		$app['controller.post'] = function() use($app) {
			return new PostController($app);
		};
		$app['controller.user'] = function() use($app) {
			return new UserController($app);
		};
		$app['controllers.provider'] = $this;
	}
}
