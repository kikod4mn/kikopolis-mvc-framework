<?php

declare(strict_types = 1);

namespace tests\Core;

use Closure;
use Kikopolis\Core\Router;
use PHPUnit\Framework\TestCase;
use function count;
use function var_dump;

/**
 * Class RouterTest
 * @package tests\Core
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
final class RouterTest extends TestCase {
	public function testRouterGetAcceptsClosure(): void {
		$router = new Router();
		$router->get('/test', fn () => 'Test of closure');
		RouterTest::assertCount(1, $router->getAll());
		RouterTest::assertTrue($router->getAll()['/test']->uri === '/test');
		RouterTest::assertTrue($router->getAll()['/test']->method === 'GET');
		RouterTest::assertTrue($router->getAll()['/test']->params === []);
		RouterTest::assertTrue($router->getAll()['/test']->callback instanceof Closure);
	}
}