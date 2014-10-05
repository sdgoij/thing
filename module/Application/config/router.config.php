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
                    'controller' => 'Application\Controller\Link',
                    'action' => 'list',
                ],
            ],
        ],
        'submit' => [
            'type' => 'literal',
            'options' => [
                'route' => '/submit',
                'defaults' => [
                    'controller' => 'Application\Controller\Link',
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
                    'controller' => 'Application\Controller\Link',
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
                    'controller' => 'Application\Controller\Comment',
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
                    'controller' => 'Application\Controller\Comment',
                    'action' => 'discussion',
                ],
            ],
        ],
        'register' => [
            'type' => 'literal',
            'options' => [
                'route' => '/register',
                'defaults' => [
                    'controller' => 'Application\Controller\User',
                    'action' => 'register',
                ],
            ],
        ],
        'login' => [
            'type' => 'literal',
            'options' => [
                'route' => '/login',
                'defaults' => [
                    'controller' => 'Application\Controller\User',
                    'action' => 'login',
                ],
            ],
        ],
        'logout' => [
            'type' => 'literal',
            'options' => [
                'route' => '/logout',
                'defaults' => [
                    'controller' => 'Application\Controller\User',
                    'action' => 'logout',
                ],
            ],
        ],
    ],
];
