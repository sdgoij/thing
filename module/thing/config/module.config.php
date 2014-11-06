<?php
return [
	'router' => require __DIR__ . '/router.config.php',
	'service_manager' => [
		'abstract_factories' => [
			'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
			'Zend\Log\LoggerAbstractServiceFactory',
		],
		'aliases' => [
			'Zend\Authentication\AuthenticationService' => 'auth',
		],
		'factories' => [
			'auth' => 'thing\factory\AuthService',
		],
	],
	'controllers' => [
		'invokables' => [
			'thing\controller\Comment' => 'thing\controller\CommentController',
			'thing\controller\Link' => 'thing\controller\LinkController',
			'thing\controller\User' => 'thing\controller\UserController',
			'thing\controller\Import' => 'thing\controller\ImportController',
		],
	],
	'view_manager' => [
		'display_not_found_reason' => true,
		'display_exceptions' => true,
		'doctype' => 'HTML5',
		'not_found_template' => 'error/404',
		'exception_template' => 'error/index',
		'template_map' => [
			'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
			'thing/link/list' => __DIR__ . '/../view/thing/link/list.phtml',
			'error/404' => __DIR__ . '/../view/error/404.phtml',
			'error/index' => __DIR__ . '/../view/error/index.phtml',
		],
		'template_path_stack' => [
			__DIR__ . '/../view',
		],
	],
	'view_helpers' => [
		'invokables' => [
			'timeago' => 'thing\view\helper\TimeAgo',
		],
	],
	'console' => [
		'router' => [
			'routes' => [],
		],
	],
	'doctrine' => [
		'driver' => [
			'thing_entities' => [
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => [__DIR__ . '/../src/thing/entity']
			],
			'orm_default' => [
				'drivers' => [
					'thing\entity' => 'thing_entities',
				],
			],
		],
	],
];
