<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Closure;
use Kikopolis\Core\Contracts\Router\RouterInterface;
use Kikopolis\Core\Router\Route;
use Kikopolis\Core\Router\RouteCollection;

/**
 * Class Router
 * @package Kikopolis
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
final class Router implements RouterInterface {
	private RouteCollection $routes;
	
	public function __construct() {
		$this->routes = new RouteCollection();
	}
	
	public function get(string $uri, Closure|array $params = []): void {
		if ($params instanceof Closure) {
			$this->routes->add($uri, new Route($uri, 'GET', callback: $params));
		} else {
			$this->routes->add($uri, new Route($uri, 'GET', $params));
		}
	}
	
	public function post(string $uri, Closure|array $params = []): void {
		if ($params instanceof Closure) {
			$this->routes->add($uri, new Route($uri, 'POST', callback: $params));
		} else {
			$this->routes->add($uri, new Route($uri, 'GET', $params));
		}
	}
	
	public function getAll(): RouteCollection {
		return $this->routes;
	}
}