<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Exception;
use Kikopolis\Core\Contracts\Router\RouterInterface;
use Kikopolis\Core\Exception\ControllerNotFoundException;
use Kikopolis\Core\Router\Exception\BadlyConfiguredRouteException;
use Kikopolis\Core\Router\Route;
use Kikopolis\View\Exception\TemplateDoesNotExistException;
use Kikopolis\View\TemplateFileLoader;
use Kikopolis\View\View;
use Throwable;
use function call_user_func;
use function class_exists;
use function file_exists;
use function file_get_contents;
use function method_exists;
use function sprintf;
use function var_dump;

final class Application {
	private RouterInterface    $router;
	private Request            $request;
	private View               $view;
	private ?Response          $response    = null;
	private Container          $container;
	private static Application $app;
	private static string      $projectPath = '';
	
	public function __construct(string $projectPath) {
		self::$projectPath = $projectPath;
		Application::$app  = $this;
		$this->router      = new Router();
		$this->view        = new View(new TemplateFileLoader());
		$this->request     = new Request($_SERVER);
		$this->container   = new Container();
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
		try {
			return $this->handleRoute($this->router->resolve($this->request));
		} catch (Exception $e) {
			if (Application::isDev() || Application::isDebug()) {
				throw new $e;
			}
			//todo log the fault in prod
			echo $this->view->render('errors.404');
			return $this->createResponse(404);
		}
	}
	
	public static function getApp(): Application {
		return self::$app;
	}
	
	public function handleRoute(Route $route): Response {
		// Priority if conflicting items are set - Callback, Controller, Template
		if (isset($route->callback)) {
			return (new Response())->setContent(call_user_func($route->callback));
		}
		if (isset($route->params['controller']) && ! isset($route->params['action'])) {
			//todo dep-injection with invokable controller
			return new Response();
		}
		if (isset($route->params['controller']) && isset($route->params['action'])) {
			//todo dep-injection with regular controller and method
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
	
	public static function getProjectPath(): string {
		if (self::$projectPath === '') {
			self::$projectPath = dirname(__DIR__);
		}
		return self::$projectPath;
	}
	
	public static function isDev(): bool {
		return true;
	}
	
	public static function isDebug(): bool {
		return true;
	}
	
	private function createResponse(int $code): Response {
		$response = new Response();
		$response->setStatusCode($code);
		return $response;
	}
}