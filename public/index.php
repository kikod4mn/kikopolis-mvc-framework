<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Kikopolis\Controller\Controller;
use Kikopolis\Core\Application;

$app = new Application(dirname(__DIR__));
$app->getRouter()->get('/', [Controller::class, 'home']);
$app->getRouter()->get('/contact', 'contact');
$app->getRouter()->get('/pricing', 'pricing');

$app->run();