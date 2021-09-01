<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Kikopolis\Core\Contracts\Router\RouterInterface;

/**
 * Class Application
 * @package Kikopolis\Application
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
final class Application {
	private RouterInterface $router;
	
	public function __construct() {
		$this->router = new Router();
	}
	
	public function getRouter(): RouterInterface {
		return $this->router;
	}
}