<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Kikopolis\Core\Contracts\Router\RouterInterface;
use Kikopolis\Core\Router\Exception\BadlyConfiguredRouteException;
use Kikopolis\Core\Router\Route;
use Kikopolis\View\Exception\TemplateDoesNotExistException;
use Kikopolis\View\View;
use function call_user_func;
use function file_exists;
use function file_get_contents;
use function sprintf;

final class Application {
	private RouterInterface $router;
	private Request         $request;
	private static string   $projectPath = '';
	private View            $view;
	
	public function __construct(string $projectPath) {
		self::$projectPath = $projectPath;
		$this->router      = new Router();
		$this->view        = new View();
		$this->request     = new Request($_SERVER);
	}
	
	public static function getProjectPath(): string {
		if (self::$projectPath === '') {
			self::$projectPath = dirname(__DIR__);
		}
		return self::$projectPath;
	}
	
	public function getRouter(): RouterInterface {
		return $this->router;
	}
	
	public function getRequest(): Request {
		return $this->request;
	}
	
	public function run() {
		$this->handleRoute($this->router->resolve($this->request));
	}
	
	public function handleRoute(Route $route): void {
		// Priority if conflicting items are set - Callback, Controller, Template
		if (isset($route->callback)) {
			call_user_func($route->callback);
			return;
		}
		if (isset($route->params['controller']) && ! isset($route->params['action'])) {
			//todo dep-injection with invokable controller
			return;
		}
		if (isset($route->params['controller']) && isset($route->params['action'])) {
			//todo dep-injection with regular controller and method
			return;
		}
		if (isset($route->template)) {
			echo $this->view->file($route->template)->render();
			return;
		}
		throw new BadlyConfiguredRouteException();
	}
}