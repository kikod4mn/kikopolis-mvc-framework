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
		$route = $router->all()['GET']['/test'];
		RouterTest::assertEquals('/test', $route->uri);
		RouterTest::assertEquals('GET', $route->method);
		RouterTest::assertEquals([], $route->params);
		RouterTest::assertInstanceOf(Closure::class, $route->callback);
	}
	
	/**
	 * @dataProvider routerParamProvider
	 */
	public function testRouterParameterParsing(string $uri, string $method, Closure|array|string $params): void {
		$router = new Router();
		$router->add($uri, $method, $params);
		$method = strtoupper($method);
		$route  = $router->all()[$method][$uri];
		RouterTest::assertTrue($route->uri === $uri);
		RouterTest::assertTrue($route->method === strtoupper($method));
		if ($params instanceof Closure) {
			RouterTest::assertEquals(42, call_user_func($route->callback));
		} else {
			if (is_array($params) || is_string($params) && str_contains($params, '@')) {
				RouterTest::assertEquals(['controller' => 'App\\CoolController', 'action' => 'someMethod'], $route->params);
			} else {
				RouterTest::assertEquals($params, $route->template);
			}
		}
	}
	
	public function routerParamProvider(): Generator {
		$controller = 'App\\CoolController';
		$action     = 'someMethod';
		yield [
			'uri'    => 'route-home',
			'method' => 'get',
			'params' => 'App\\CoolController@someMethod',
		];
		yield [
			'uri'    => 'route-home',
			'method' => 'get',
			'params' => 'home',
		];
		yield [
			'uri'    => '/route-home',
			'method' => 'get',
			'params' => [$controller, $action],
		];
		yield [
			'uri'    => '/route-home',
			'method' => 'GET',
			'params' => ['controller' => $controller, 'action' => $action],
		];
		yield [
			'uri'    => '/route-home',
			'method' => 'post',
			'params' => fn () => 42,
		];
		yield [
			'uri'    => '/route-home',
			'method' => 'put',
			'params' => fn () => 42,
		];
		yield [
			'uri'    => '/route-home',
			'method' => 'patch',
			'params' => fn () => 42,
		];
		yield [
			'uri'    => '/route-home',
			'method' => 'delete',
			'params' => fn () => 42,
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