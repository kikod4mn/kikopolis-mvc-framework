<?php

declare(strict_types = 1);

namespace Core;

use App\Controller\Controller;
use Kikopolis\Core\Container;
use Kikopolis\Core\Request;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase {
	public function testGet(): void {
		$container = new Container();
		ContainerTest::assertInstanceOf(Request::class, $container->get(Request::class));
	}
	
	public function testGetMethodParams(): void {
		$container = new Container();
		// todo figure out a better solution than using the Controller class for testing
		ContainerTest::assertInstanceOf(Request::class, $container->getMethodParameters(new Controller(), 'handleContact')[0]);
	}
}