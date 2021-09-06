<?php

declare(strict_types = 1);

namespace Tests\Core;

use Closure;
use Generator;
use Kikopolis\Core\Request;
use Kikopolis\Core\Router;
use PHPUnit\Framework\TestCase;
use function is_string;
use function str_contains;
use function strtoupper;

final class RouterTest extends TestCase {
	public function testGet(): void {
		$router = new Router();
		$router->get('/test', fn () => 'Test of closure');
		$route = $router->resolve(new Request(['REQUEST_METHOD' => 'get', 'REQUEST_URI' => '/test']));
		RouterTest::assertEquals('/test', $route->uri);
		RouterTest::assertEquals('GET', $route->method);
		RouterTest::assertEquals([], $route->params);
		RouterTest::assertInstanceOf(Closure::class, $route->callback);
	}
	
	/**
	 * @dataProvider routerParamProvider
	 */
	public function testRouterParameterParsing(string $uri, string $requestUri, string $method, Closure|array|string $params): void {
		$router = new Router();
		$router->add($uri, $method, $params);
		$method = strtoupper($method);
		$route  = $router->resolve(new Request(['REQUEST_METHOD' => $method, 'REQUEST_URI' => $requestUri]));
		RouterTest::assertTrue($route->uri === $uri);
		RouterTest::assertTrue($route->method === strtoupper($method));
		if ($params instanceof Closure) {
			RouterTest::assertEquals(42, call_user_func($route->callback));
		} else {
			if (is_array($params) || is_string($params) && str_contains($params, '@')) {
				RouterTest::assertEquals(['controller' => 'App\\Controller\\CoolController', 'action' => 'actionMethod'], $route->params);
			} else {
				RouterTest::assertEquals($params, $route->template);
			}
		}
	}
	
	public function routerParamProvider(): Generator {
		$controllerWithNamespace = 'App\\Controller\\CoolController';
		$action                  = 'actionMethod';
		yield 'Test a fully namespaced controller with @ notation method as a string' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home',
			'method'     => 'get',
			'params'     => sprintf("%s@%s", $controllerWithNamespace, $action),
		];
		yield 'Test a template file' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home',
			'method'     => 'get',
			'params'     => 'home',
		];
		yield 'Test an array of [$controller, $action]' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home',
			'method'     => 'get',
			'params'     => [$controllerWithNamespace, $action],
		];
		yield 'Test an array of [$controller, $action] with named keys' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home',
			'method'     => 'GET',
			'params'     => ['controller' => $controllerWithNamespace, 'action' => $action],
		];
		yield 'Test a callback with 42 with POST' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home',
			'method'     => 'post',
			'params'     => fn () => 42,
		];
		yield 'Test a callback with 42 with PUT' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home',
			'method'     => 'put',
			'params'     => fn () => 42,
		];
		yield 'Test a callback with 42 with PATCH' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home',
			'method'     => 'patch',
			'params'     => fn () => 42,
		];
		yield 'Test a callback with 42 with DELETE' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home',
			'method'     => 'delete',
			'params'     => fn () => 42,
		];
		yield 'Test a query string with callback 42' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home&id=1',
			'method'     => 'delete',
			'params'     => fn () => 42,
		];
		yield 'Test that a controller without namespace gets the default namespace' => [
			'uri'        => '/route-home',
			'requestUri' => '/route-home&id=1',
			'method'     => 'delete',
			'params'     => ['CoolController', $action],
		];
		yield 'Test with multiple route parameters' => [
			'uri'        => '/route-home/users/{id}/posts/{id}',
			'requestUri' => '/route-home/users/42/posts/247',
			'method'     => 'delete',
			'params'     => ['CoolController', $action],
		];
	}
	
	/**
	 * @dataProvider resolveProvider
	 */
	public function testResolve(string $path, array $route): void {
		$request = $this->getMockBuilder(Request::class)
						->setConstructorArgs(['server' => ['REQUEST_URI' => $path, 'REQUEST_METHOD' => strtoupper($route['method'])]])
						->getMock();
		$request->expects(RouterTest::once())
				->method('getPath')
				->willReturn($path)
		;
		$request->expects(RouterTest::once())
				->method('getMethod')
				->willReturn(strtoupper($route['method']))
		;
		$router = new Router();
		$router->add($route['uri'], $route['method'], $route['params']);
		$resolvedRoute = $router->resolve($request);
		RouterTest::assertEquals($route['uri'], $resolvedRoute->uri);
		RouterTest::assertEquals(strtoupper($route['method']), $resolvedRoute->method);
		RouterTest::assertEquals(42, call_user_func($resolvedRoute->callback));
	}
	
	public function resolveProvider(): Generator {
		yield [
			'path'  => '/test',
			'route' => ['uri' => '/test', 'method' => 'get', 'params' => fn () => 42],
		];
	}
}