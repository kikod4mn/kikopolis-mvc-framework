<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Closure;
use Kikopolis\Core\Collection\Collection;
use Kikopolis\Core\Collection\MixedCollection;
use Kikopolis\Core\Collection\RouteCollection;
use Kikopolis\Core\Contracts\Router\RouterInterface;
use Kikopolis\Core\Router\Exception\BadlyConfiguredRouteException;
use Kikopolis\Core\Router\Exception\InvalidRouteMethodException;
use Kikopolis\Core\Router\Exception\NoRouteMatchException;
use Kikopolis\Core\Router\Route;
use Kikopolis\Support\Arr;
use Kikopolis\View\Exception\TemplateDoesNotExistException;
use function array_shift;
use function call_user_func;
use function in_array;
use function mb_strcut;
use function mb_strlen;
use function sprintf;
use function str_contains;
use function strpos;
use function strtoupper;

final class Router implements RouterInterface {
	const GET    = 'GET';
	const POST   = 'POST';
	const PUT    = 'PUT';
	const PATCH  = 'PATCH';
	const DELETE = 'DELETE';
	
	private Collection $routes;
	private array      $routeParamNames = ['controller', 'action'];
	private array      $routeMethods    = [self::GET, self::POST, self::PUT, self::PATCH, self::DELETE];
	
	public function __construct() {
		$this->routes = new MixedCollection();
		foreach ($this->routeMethods as $routeMethod) {
			$this->routes->put($routeMethod, new RouteCollection());
		}
	}
	
	public function add(string $uri, string $method, Closure|array|string $params = []): void {
		$method = $this->parseMethod($method);
		$this->routes[$method]->put($uri, new Route($uri, $method, $this->parseParams($params)));
	}
	
	public function get(string $uri, Closure|array|string $params = []): void {
		$this->routes[self::GET]->put($uri, new Route($uri, self::GET, $this->parseParams($params)));
	}
	
	public function post(string $uri, Closure|array|string $params = []): void {
		$this->routes[self::POST]->put($uri, new Route($uri, self::GET, $this->parseParams($params)));
	}
	
	public function all(): Collection {
		return $this->routes;
	}
	
	public function resolve(Request $request): Route {
		return $this->match($request);
	}
	
	private function match(Request $request): Route {
		$route = $this->routes[$request->getMethod()][$request->getPath()] ?? null;
		if ($route === null) {
			throw new NoRouteMatchException();
		}
		return $route;
	}
	
	private function parseParams(Closure|array|string $params): array {
		if ($params instanceof Closure) {
			return ['callback' => $params];
		}
		$result = [];
		if (is_string($params)) {
			// This is if using an @ notation with Controller@action
			if (str_contains($params, '@')) {
				$pos = strpos($params, '@');
				return ['controller' => mb_strcut($params, 0, $pos), 'action' => mb_strcut($params, $pos + 1, mb_strlen($params))];
			} else {
				// Allow to call a name of the template to render if it exists
				return ['template' => $params];
			}
		} else {
			if (Arr::associative($params)) {
				foreach ($this->routeParamNames as $routeParam) {
					$result[$routeParam] = array_shift($params);
				}
			} else {
				foreach ($params as $key => $value) {
					if (in_array($key, $this->routeParamNames)) {
						$result[$key] = $value;
					}
				}
			}
		}
		return $result;
	}
	
	private function parseMethod(string $method): string {
		$method = strtoupper($method);
		if (! in_array($method, $this->routeMethods)) {
			throw new InvalidRouteMethodException();
		}
		return $method;
	}
}