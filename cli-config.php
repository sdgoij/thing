<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
$app = require 'bootstrap.php';

if (!is_dir('data')) mkdir('data');

return ConsoleRunner::createHelperSet($app['orm.em']);
