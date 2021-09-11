<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Exception;
use Kikopolis\Core\Contracts\Router\RouterInterface;
use Kikopolis\Core\Exception\ControllerNotFoundException;
use Kikopolis\Core\Router\Exception\BadlyConfiguredRouteException;
use Kikopolis\Core\Router\Route;
use Kikopolis\View\View;
use function call_user_func;
use function class_exists;
use function config;
use function dd;
use function dirname;
use function ini_set;
use function method_exists;
use function set_error_handler;
use function set_exception_handler;
use function sprintf;
use const E_ALL;
use const E_DEPRECATED;

final class Application {
	private ?Response          $response    = null;
	private RouterInterface    $router;
	private Request            $request;
	private View               $view;
	private Container          $container;
	private static Application $app;
	private static string      $projectPath = '';
	
	public function __construct() {
		self::$projectPath = dirname(__DIR__);
		Application::$app  = $this;
		$this->container   = new Container();
		$this->router      = $this->container->get('router');
		$this->view        = $this->container->get('template_engine');
		$this->request     = new Request($_SERVER);
		config()->loadRoutes();
	}
	
	public function getRouter(): RouterInterface {
		return $this->router;
	}
	
	public function getView(): View {
		return $this->view;
	}
	
	public function getRequest(): Request {
		return $this->request;
	}
	
	public function getResponse(): Response {
		if (is_null($this->response)) {
			$this->response = new Response();
		}
		return $this->response;
	}
	
	public function getContainer(): Container {
		return $this->container;
	}
	
	public function run(): Response {
		$this->errorReporting();
		return $this->handleRoute($this->router->resolve($this->request));
	}
	
	public static function getApp(): Application {
		return self::$app;
	}
	
	public static function getProjectPath(): string {
		return self::$projectPath;
	}
	
	public static function isDev(): bool {
		return true;
	}
	
	public static function isDebug(): bool {
		return true;
	}
	
	private function errorReporting(): void {
		if (self::isDebug()) {
			ini_set('display_errors', 'On');
			ini_set('error_reporting', (string) E_ALL);
			set_error_handler('Kikopolis\Core\ErrorHandler\ErrorHandler::errorHandler');
			set_exception_handler('Kikopolis\Core\ErrorHandler\ErrorHandler::exceptionHandler');
			ini_set("xdebug.var_display_max_children", '-1');
			ini_set("xdebug.var_display_max_data", '-1');
			ini_set("xdebug.var_display_max_depth", '-1');
		} else {
			ini_set('error_reporting', (string) (E_ALL ^ E_DEPRECATED));
			set_error_handler('Kikopolis\Core\ErrorHandler\ErrorHandler::errorHandler');
			set_exception_handler('Kikopolis\Core\ErrorHandler\ErrorHandler::exceptionHandler');
		}
	}
	
	private function handleRoute(Route $route): Response {
		// Priority if conflicting items are set - Callback, Controller, Template
		if (isset($route->callback)) {
			return (new Response())->setContent(call_user_func($route->callback));
		}
		if (isset($route->params['controller']) && ! isset($route->params['action'])) {
			//todo dep-injection with invokable controller
			return new Response();
		}
		if (isset($route->params['controller']) && isset($route->params['action'])) {
			if (! class_exists($route->params['controller']) || ! method_exists($route->params['controller'], $route->params['action'])) {
				throw new ControllerNotFoundException(
					sprintf('Controller "%s" or method "%s" does not exist.', $route->params['controller'], $route->params['action'])
				);
			}
			$controller   = $this->getContainer()->get($route->params['controller']);
			$methodParams = $this->getContainer()->getMethodParameters($controller, $route->params['action']) ?? [];
			return call_user_func([$controller, $route->params['action']], $methodParams);
		}
		if (isset($route->template)) {
			return (new Response())->setContent($this->view->render($route->template));
		}
		throw new BadlyConfiguredRouteException();
	}
	
	private function createResponse(int $code): Response {
		$response = new Response();
		$response->setStatusCode($code);
		return $response;
	}
}