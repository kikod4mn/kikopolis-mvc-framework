<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Router;

use Closure;

/**
 * Class Route
 * @package Kikopolis\Core\Router
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
final class Route {
	public ?Closure $callback = null;
	public ?string  $template = null;
	public array    $params   = [];
	
	public function __construct(public string $uri, public string $method, array $params) {
		$this->setParams($params);
	}
	
	private function setParams(array $params): void {
		if (isset($params['controller'], $params['action'])) {
			$this->params = $params;
		}
		if (isset($params['template'])) {
			$this->template = $params['template'];
		}
		if (isset($params['callback'])) {
			$this->callback = $params['callback'];
		}
	}
}
