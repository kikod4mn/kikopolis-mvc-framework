<?php

declare(strict_types = 1);

require_once __DIR__ . "/vendor/autoload.php";

use Kikopolis\Core\Application;

$app = new Application();
$app->getRouter()->get('/', fn () => 'Hello World');

$app->run();