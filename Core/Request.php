<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Kikopolis\Core\Router\Exception\InvalidRouteMethodException;
use function mb_strcut;
use function mb_strpos;
use function str_contains;
use function str_replace;
use function strtoupper;

final class Request {
	const GET    = 'GET';
	const POST   = 'POST';
	const PUT    = 'PUT';
	const PATCH  = 'PATCH';
	const DELETE = 'DELETE';
	
	private string $path;
	private string $method;
	private array  $server;
	
	public function __construct(array $server) {
		$this->server = $server;
	}
	
	public function getPath(): string {
		if (! isset($this->path)) {
			$this->path = $this->server['REQUEST_URI'];
//			$this->path = $this->parsePath($this->server['REQUEST_URI']);
		}
		return $this->path;
	}
	
	public function getMethod(): string {
		if (! isset($this->method)) {
			$this->method = $this->parseMethod($this->server['REQUEST_METHOD']);
		}
		return $this->method;
	}
	
	public function getBody() {
	
	}
	
	private function parsePath(string $path): string {
		if (str_contains($path, '&')) {
			$path = str_replace('&', '?', $path);
		}
		if (isset($path) && $path !== '') {
			if (str_contains($path, '?')) {
				return mb_strcut($path, 0, mb_strpos($path, '?'));
			}
			return $path;
		} else {
			return '/';
		}
	}
	
	private function parseMethod(string $method): string {
		$method = strtoupper($method);
		if (in_array($method, [self::GET, self::POST, self::PUT, self::PATCH, self::DELETE])) {
			return $method;
		}
		throw new InvalidRouteMethodException();
	}
}