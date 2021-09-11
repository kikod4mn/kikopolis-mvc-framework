<?php

declare(strict_types = 1);

namespace Core;

use Kikopolis\Core\Application;
use Kikopolis\Core\Container;
use Kikopolis\Core\Contracts\RendererInterface;
use Kikopolis\Core\Contracts\Router\RouterInterface;
use Kikopolis\Core\Request;
use Kikopolis\Core\Response;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase {
	public function testApplicationInitializes(): void {
		ApplicationTest::assertInstanceOf(Application::class, new Application());
	}
	
	public function testGetRouter(): void {
		$app = new Application();
		ApplicationTest::assertInstanceOf(RouterInterface::class, $app->getRouter());
	}
	
	public function testGetView(): void {
		$app = new Application();
		ApplicationTest::assertInstanceOf(RendererInterface::class, $app->getView());
	}
	
	public function testGetRequest() {
		$app = new Application();
		ApplicationTest::assertInstanceOf(Request::class, $app->getRequest());
	}
	
	public function testGetResponse() {
		$app = new Application();
		ApplicationTest::assertInstanceOf(Response::class, $app->getResponse());
	}
	
	public function testGetContainer() {
		$app = new Application();
		ApplicationTest::assertInstanceOf(Container::class, $app->getContainer());
	}
}