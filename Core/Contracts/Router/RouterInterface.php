<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Contracts\Router;

use Closure;
use Kikopolis\Core\Collection\Collection;

interface RouterInterface {
	public function get(string $uri, array|Closure $params = []): void;
	
	public function post(string $uri, array|Closure $params = []): void;
	
	public function getAll(): Collection;
}