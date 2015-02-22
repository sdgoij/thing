<?php
return [
	'router' => require __DIR__ . '/router.config.php',
	'service_manager' => [
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
		'template_path_stack' => [
			__DIR__ . '/../view',
		],
	],
	'view_helpers' => [
		'invokables' => [
			'timeago' => 'thing\view\helper\TimeAgo',
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
