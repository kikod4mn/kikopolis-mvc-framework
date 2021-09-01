<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Contracts\Router;

use Closure;
use Kikopolis\Core\Collection\Collection;

/**
 * Interface RouterInterface
 * @package Kikopolis\Contracts
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
interface RouterInterface {
	public function get(string $uri, array|Closure $params = []): void;
	
	public function post(string $uri, array|Closure $params = []): void;
	
	public function getAll(): Collection;
}