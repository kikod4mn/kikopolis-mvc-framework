<?php

declare(strict_types = 1);

use Kikopolis\Core\Application;
use Kikopolis\Core\Config;
use Kikopolis\Core\Request;
use Kikopolis\Core\Router;
use Kikopolis\View\View;

if (! function_exists('dd')) {
	function dd(...$vars): void {
		foreach ($vars as $var) {
			echo "<pre>";
			echo htmlspecialchars(var_export($var, true));
			echo "</pre>";
		}
		die;
	}
}

if (! function_exists('app')) {
	function app(): Application {
		return Application::getApp();
	}
}

if (! function_exists('request')) {
	function request(): Request {
		return app()->getRequest();
	}
}

if (! function_exists('router')) {
	function router(): Router {
		return app()->getRouter();
	}
}

if (! function_exists('view')) {
	function view(): View {
		return app()->getView();
	}
}

if (! function_exists('config')) {
	function config(): Config {
		return app()->getConfig();
	}
}
