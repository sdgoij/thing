<?php chdir(__DIR__);

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;

if (!ini_get('date.timezone')) {
	ini_set('date.timezone', 'UTC');
}

if (!defined('DATA_DIR')) {
	define('DATA_DIR', 'data');
}

if (!defined('APP_ENV_DEV')) {
	define('APP_ENV_DEV', getenv('THING_DEVELOPMENT') ? true : false);
}

$loader = require './vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$app = new Application();
$app->register(new MonologServiceProvider(), [
	'monolog.logfile' => DATA_DIR . '/app.log',
	'monolog.name'    => 'thing',
]);
$app->register(new DoctrineServiceProvider(), [
	"db.options" => [
		"driver" => "pdo_sqlite",
		"path"   => DATA_DIR . "/thing.db",
	],
]);
$app->register(new DoctrineOrmServiceProvider(), [
	"orm.proxies_dir" => DATA_DIR . "/proxies",
	"orm.em.options"  => [
		"mappings" => [
			[
				"type"      => "annotation",
				"namespace" => "thing\\entity",
				"path"      => "src/entity",
				/**
				 * This and make sure the project autoloader is added to Doctrine AnnotationRegistry
				 * @see https://github.com/dflydev/dflydev-doctrine-orm-service-provider#why-arent-my-annotations-classes-being-found
				 */
				"use_simple_annotation_reader" => false,
			],
		],
	],
]);
unset($loader);
return $app;
