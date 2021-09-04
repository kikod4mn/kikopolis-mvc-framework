<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Kikopolis\Core\Contracts\Router\RouterInterface;

final class Application {
	private RouterInterface $router;
	
	public function __construct() {
		$this->router = new Router();
	}
	
	public function getRouter(): RouterInterface {
		return $this->router;
	}
}