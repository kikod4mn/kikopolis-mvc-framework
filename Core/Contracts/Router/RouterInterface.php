<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Contracts\Router;

use Closure;
use Kikopolis\Core\Collection\Collection;
use Kikopolis\Core\Request;

interface RouterInterface {
	public function add(string $uri, string $method, Closure|array|string $params = []): void;
	
	public function all(): Collection;
	
	public function resolve(Request $request);
}