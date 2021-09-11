<?php

declare(strict_types = 1);

use Kikopolis\Core\Request;
use Kikopolis\Core\Response;
use Kikopolis\Core\Router;
use Kikopolis\View\TemplateFileLoader;
use Kikopolis\View\View;

return [
	Request::class            => Request::class,
	Response::class           => Response::class,
	Router::class             => Router::class,
	'router'                  => Router::class,
	'template_engine'         => View::class,
	View::class               => View::class,
	'template_loader'         => TemplateFileLoader::class,
	TemplateFileLoader::class => TemplateFileLoader::class,
];
