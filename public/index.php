<?php

use Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapBadgeExtension;
use Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapFormExtension;
use Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapIconExtension;
use Braincrafted\Bundle\BootstrapBundle\Twig\BootstrapLabelExtension;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use thing\event\subscriber\PaginateDoctrineSelectable;
use thing\provider\ControllerServiceProvider;
use thing\provider\PaginationServiceProvider;
use thing\provider\SecurityServiceProvider;

if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
	return false;
}

/** @var Application $app */
$app = require dirname(__DIR__) . '/bootstrap.php';

$app->register(new FormServiceProvider());
$app->register(new TwigServiceProvider(), [
	'twig.path' => [
		'templates',
		'vendor/braincrafted/bootstrap-bundle/Braincrafted/Bundle/BootstrapBundle/Resources/views/Form',
	],
	'twig.options' => [
		'cache' => DATA_DIR . '/twig',
	],
	'twig.form.templates' => ['bootstrap.html.twig'],
]);

$app['twig'] = $app->extend('twig', function(\Twig_Environment $twig, Application $app) {
	$twig->addFunction(new \Twig_SimpleFunction('asset', function($asset) use($app) {
		return $app['request_stack']->getCurrentRequest()->getBasePath() . '/' . ltrim($asset, '/');
	}));
	$twig->addExtension(new BootstrapIconExtension('icon'));
	$twig->addExtension(new BootstrapLabelExtension());
	$twig->addExtension(new BootstrapBadgeExtension());
	$twig->addExtension(new BootstrapFormExtension());
	return $twig;
});

$app->register(new ControllerServiceProvider());
$app->register(new PaginationServiceProvider(), [
	'knp_paginator.listeners' => [
		new PaginateDoctrineSelectable()
	]
]);

$app->register(new SecurityServiceProvider());

if (APP_ENV_DEV) {
	$app->register(new HttpFragmentServiceProvider());
	$app->register(new ServiceControllerServiceProvider());
	$app->register(new RoutingServiceProvider());
	$app->register(new WebProfilerServiceProvider(), [
		'profiler.cache_dir' => DATA_DIR . '/profiler',
		'profiler.mount_prefix' => '/_profiler',
	]);
}

$app->mount('/', $app['controllers.provider']);
$app->run();
