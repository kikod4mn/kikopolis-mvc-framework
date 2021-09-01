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
	public function __construct(public string $uri, public string $method, public array $params = [], public ?Closure $callback = null) { }
}