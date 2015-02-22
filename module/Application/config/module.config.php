<?php
return [
	'router' => [
		'routes' => [
			'__root__' => [
				'type' => 'segment',
				'options' => [
					'route' => '/',
					'defaults' => [
						'controller' => 'Application\Controller\Index',
						'action' => 'list',
					],
				],
			],
		],
	],
	'service_manager' => [
		'abstract_factories' => [
			'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
			'Zend\Log\LoggerAbstractServiceFactory',
		],
	],
	'controllers' => [
		'invokables' => [
			'Application\Controller\Index' => 'Application\Controller\IndexController',
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
			'error/404' => __DIR__ . '/../view/error/404.phtml',
			'error/index' => __DIR__ . '/../view/error/index.phtml',
		],
		'template_path_stack' => [
			__DIR__ . '/../view',
		],
	],
	'view_helpers' => [
		'invokables' => [
		],
	],
	'console' => [
		'router' => [
			'routes' => [],
		],
	],
];
