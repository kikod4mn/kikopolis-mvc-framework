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
use function count;
use function dd;
use function in_array;
use function mb_strcut;
use function mb_strlen;
use function mb_strpos;
use function preg_match;
use function preg_replace;
use function rtrim;
use function sprintf;
use function str_contains;
use function str_replace;
use function strpos;
use function strtoupper;
use function trim;
use function var_dump;

final class Router implements RouterInterface {
	const GET    = 'GET';
	const POST   = 'POST';
	const PUT    = 'PUT';
	const PATCH  = 'PATCH';
	const DELETE = 'DELETE';
	
	private Collection $routes;
	private array      $routeParamNames = ['controller', 'action'];
	private array      $routeMethods    = [self::GET, self::POST, self::PUT, self::PATCH, self::DELETE];
	private string     $namespace       = 'App\\Controller\\';
	
	public function __construct() {
		$this->routes = new MixedCollection();
		foreach ($this->routeMethods as $routeMethod) {
			$this->routes->put($routeMethod, new RouteCollection());
		}
	}
	
	public function add(string $uri, string $method, Closure|array|string $params = []): void {
		$method = $this->parseMethod($method);
		$this->routes[$method]->put($this->parseUrlToRegex($uri), new Route($uri, $method, $this->parseParams($params)));
	}
	
	public function get(string $uri, Closure|array|string $params = []): void {
		$this->routes[self::GET]->put($this->parseUrlToRegex($uri), new Route($uri, self::GET, $this->parseParams($params)));
	}
	
	public function post(string $uri, Closure|array|string $params = []): void {
		$this->routes[self::POST]->put($this->parseUrlToRegex($uri), new Route($uri, self::GET, $this->parseParams($params)));
	}
	
	public function all(): Collection {
		return $this->routes;
	}
	
	public function resolve(Request $request): Route {
		return $this->match($request);
	}
	
	private function match(Request $request): Route {
		$matched = null;
		$uri     = $this->removeQueryVariables($request);
		/** @var Route $route */
		foreach ($this->routes[$request->getMethod()]->toArray() as $regex => $route) {
			if (preg_match($regex, $uri) === 1) {
				$matched = $route;
			}
		}
//		$route = $this->routes[$request->getMethod()][$this->removeQueryVariables($request)] ?? null;
		if ($matched === null) {
			throw new NoRouteMatchException();
		}
		return $matched;
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
				return ['controller' => $this->controllerNamespace(mb_strcut($params, 0, $pos)), 'action' => mb_strcut($params, $pos + 1, mb_strlen($params))];
			} else {
				// Allow to call a name of the template to render if it exists
				return ['template' => $params];
			}
		} else {
			if (Arr::associative($params)) {
				// For an associative array, ASSUME key 0 is controller and 1 is action.
				// Associative array currently does not support any other parameters.
				$result['controller'] = $this->controllerNamespace(array_shift($params));
				$result['action']     = array_shift($params);
			} else {
				// If array with keys, then the key 'controller' value is used.
				foreach ($params as $key => $value) {
					if (in_array($key, $this->routeParamNames)) {
						if ($key === 'controller') {
							$result[$key] = $this->controllerNamespace($value);
						} else {
							$result[$key] = $value;
						}
					}
				}
			}
		}
		return $result;
	}
	
	public function parseUrlToRegex(string $url): string {
		// Convert the route to a regular expression: escape forward slashes
		$url = preg_replace('/\//', '\\/', $url);
		// Convert variables e.g. {id}, {slug} to capture groups
		$url = preg_replace('/{([a-z]+)}/', '(?P<\1>[a-zA-Z0-9-_]+)', $url);
		// Add start and end delimiters, and case-insensitive flag
		return '/^' . $url . '$/i';
	}
	
	private function extractRouteParameters(string $url, string $regex): array {
		$params = [];
		preg_match($regex, $url, $matches);
		if (count($matches) > 0) {
			foreach ($matches as $key => $value) {
				$params[$key] = $value;
			}
		}
		return $params;
	}
	
	private function controllerNamespace(string $controller): string {
		// Assume the namespace is present if backspaces are present.
		if (str_contains($controller, '\\')) {
			return $controller;
		}
		return sprintf("%s%s", $this->namespace, $controller);
	}
	
	private function removeQueryVariables(string|Request $request): string {
		if ($request instanceof Request) {
			$request = $request->getPath();
		}
		if (str_contains($request, '&')) {
			$request = str_replace('&', '?', $request);
		}
		if (! isset($request) || $request === '' || $request === '/') {
			return '/';
		} else {
			if (str_contains($request, '?')) {
				return mb_strcut($request, 0, mb_strpos($request, '?'));
			}
			return $request;
		}
	}
	
	private function parseMethod(string $method): string {
		$method = strtoupper($method);
		if (! in_array($method, $this->routeMethods)) {
			throw new InvalidRouteMethodException();
		}
		return $method;
	}
}