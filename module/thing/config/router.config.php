<?php
return [
	'routes' => [
		'home' => [
			'type' => 'segment',
			'options' => [
				'route' => '/[:p]',
				'constraints' => [
					'p' => '[0-9]*',
				],
				'defaults' => [
					'controller' => 'thing\controller\Link',
					'action' => 'list',
				],
			],
		],
		'submit' => [
			'type' => 'literal',
			'options' => [
				'route' => '/submit',
				'defaults' => [
					'controller' => 'thing\controller\Link',
					'action' => 'submit',
				],
			],
		],
		'goto' => [
			'type' => 'segment',
			'options' => [
				'route' => '/goto/:id',
				'constraints' => [
					'id' => '[0-9]*',
				],
				'defaults' => [
					'controller' => 'thing\controller\Link',
					'action' => 'goto',
				],
			],
		],
		'reply' => [
			'type' => 'segment',
			'options' => [
				'route' => '/reply/:link[/:parent]',
				'constraints' => [
					'link' => '[0-9]*',
					'parent' => '[0-9]*',
				],
				'defaults' => [
					'controller' => 'thing\controller\Comment',
					'action' => 'reply',
				],
			],
		],
		'discussion' => [
			'type' => 'segment',
			'options' => [
				'route' => '/discussion/:link',
				'constraints' => [
					'link' => '[0-9]*',
				],
				'defaults' => [
					'controller' => 'thing\controller\Comment',
					'action' => 'discussion',
				],
			],
		],
		'register' => [
			'type' => 'literal',
			'options' => [
				'route' => '/register',
				'defaults' => [
					'controller' => 'thing\controller\User',
					'action' => 'register',
				],
			],
		],
		'login' => [
			'type' => 'literal',
			'options' => [
				'route' => '/login',
				'defaults' => [
					'controller' => 'thing\controller\User',
					'action' => 'login',
				],
			],
		],
		'logout' => [
			'type' => 'literal',
			'options' => [
				'route' => '/logout',
				'defaults' => [
					'controller' => 'thing\controller\User',
					'action' => 'logout',
				],
			],
		],
		'import' => [
			'type' => 'literal',
			'options' => [
				'route' => '/import/hn',
				'defaults' => [
					'controller' => 'thing\controller\Import',
					'action' => 'hn',
				],
			],
		],
	],
];
