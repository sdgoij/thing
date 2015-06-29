<?php
namespace thing\provider;

use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Bundle\PaginatorBundle\Subscriber\SlidingPaginationSubscriber;
use Knp\Bundle\PaginatorBundle\Twig\Extension\PaginationExtension;
use Knp\Component\Pager\Event\Subscriber\Filtration\FiltrationSubscriber;
use Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber;
use Knp\Component\Pager\Event\Subscriber\Sortable\SortableSubscriber;
use Knp\Component\Pager\Paginator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class PaginationServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface {
	/**
	 * @param Container $app
	 */
	public function register(Container $app) {
		if (!isset($app['twig'])) {
			$app->register(new TwigServiceProvider());
		}
		if (!isset($app['url_generator'])) {
			$app->register(new RoutingServiceProvider());
		}
		if (!isset($app['locale'])) {
			$app->register(new LocaleServiceProvider());
		}
		if (!isset($app['translator'])) {
			$app->register(new TranslationServiceProvider());
		}
		$app['twig'] = $app->extend('twig', function(\Twig_Environment $twig, $app) {
			$processor = new Processor($app['url_generator'], $app['translator']);
			$twig->addExtension(new PaginationExtension($processor));
			return $twig;
		});

		$app['knp_paginator.path'] = __DIR__ . '/../../vendor/knplabs/knp-paginator-bundle';
		$app['knp_paginator.limits'] = [10, 25, 50, 100, 200, 500];
		$app['knp_paginator.listeners'] = [];
		$app['knp_paginator.options'] = [];

		$app['knp_paginator.options_fixer'] = function ($app) {
			$app['knp_paginator.options'] = array_replace_recursive(
				[
					'default_options' => [
						'sort_field_name' => 'sort',
						'sort_direction_name' => 'direction',
						'filter_field_name' => 'filterField',
						'filter_value_name' => 'filterValue',
						'page_name' => 'page',
						'distinct' => true,
					],
					'template' => [
						'pagination' => '@knp_paginator_bundle/sliding.html.twig',
						'filtration' => '@knp_paginator_bundle/filtration.html.twig',
						'sortable' => '@knp_paginator_bundle/sortable_link.html.twig',
					],
					'page_range' => 5,
				], $app['knp_paginator.options']
			);
		};
		$app['knp_paginator'] = function($app) {
			$loader = new \Twig_Loader_Filesystem();
			$loader->addPath(rtrim($app['knp_paginator.path'], '/') . '/Resources/views/Pagination', 'knp_paginator_bundle');
			$app['twig.loader']->addLoader($loader);
			$app['knp_paginator.options_fixer'];

			$paginator = new Paginator($app['dispatcher']);
			foreach ($app['knp_paginator.listeners'] as $listener) {
				$paginator->subscribe($listener);
			}
			$paginator->setDefaultPaginatorOptions([
				'pageParameterName' => $app['knp_paginator.options']['default_options']['page_name'],
				'sortFieldParameterName' => $app['knp_paginator.options']['default_options']['sort_field_name'],
				'sortDirectionParameterName' => $app['knp_paginator.options']['default_options']['sort_direction_name'],
				'filterFieldParameterName' => $app['knp_paginator.options']['default_options']['filter_field_name'],
				'filterValueParameterName' => $app['knp_paginator.options']['default_options']['filter_value_name'],
				'distinct' => $app['knp_paginator.options']['default_options']['distinct'],
			]);
			return $paginator;
		};
		$app['knp_paginator.pagination_subscriber'] = function() { return new PaginationSubscriber(); };
		$app['knp_paginator.sortable_subscriber'] = function() { return new SortableSubscriber(); };
		$app['knp_paginator.filtration_subscriber'] = function() { return new FiltrationSubscriber(); };
		$app['knp_paginator.sliding_pagination_subscriber'] = function(Container $app) {
			$app['knp_paginator.options_fixer'];
			return new SlidingPaginationSubscriber([
				'defaultPaginationTemplate' => $app['knp_paginator.options']['template']['pagination'],
				'defaultSortableTemplate' => $app['knp_paginator.options']['template']['sortable'],
				'defaultFiltrationTemplate' => $app['knp_paginator.options']['template']['filtration'],
				'defaultPageRange' => $app['knp_paginator.options']['page_range'],
			]);
		};
	}

	/**
	 * @param Container $app
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function subscribe(Container $app, EventDispatcherInterface $dispatcher) {
		$dispatcher->addSubscriber($app['knp_paginator.pagination_subscriber']);
		$dispatcher->addSubscriber($app['knp_paginator.sortable_subscriber']);
		$dispatcher->addSubscriber($app['knp_paginator.filtration_subscriber']);
		$dispatcher->addSubscriber($app['knp_paginator.sliding_pagination_subscriber']);
		$dispatcher->addListener(KernelEvents::REQUEST, [
			$app['knp_paginator.sliding_pagination_subscriber'],
			'onKernelRequest'
		]);
	}
}
