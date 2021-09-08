<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Kikopolis\Core\Router\Exception\InvalidRouteMethodException;
use function dd;
use function filter_input;
use function mb_strcut;
use function mb_strpos;
use function str_contains;
use function str_replace;
use function strtoupper;
use const FILTER_SANITIZE_SPECIAL_CHARS;
use const INPUT_GET;

final class Request {
	const GET    = 'GET';
	const POST   = 'POST';
	const PUT    = 'PUT';
	const PATCH  = 'PATCH';
	const DELETE = 'DELETE';
	
	private string $path;
	private string $method;
	private array  $server;
	
	public function __construct(array $server = []) {
		$this->server = $server === [] ? $_SERVER : $server;
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
	
	public function getBody(): array {
		$body = [];
		if ($this->getMethod() === Request::GET) {
			foreach ($_GET as $key => $value) {
				$body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}
		if ($this->getMethod() === Request::POST) {
			foreach ($_POST as $key => $value) {
				$body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}
		return $body;
	}
	
	private function parseMethod(string $method): string {
		$method = strtoupper($method);
		if (in_array($method, [self::GET, self::POST, self::PUT, self::PATCH, self::DELETE])) {
			return $method;
		}
		throw new InvalidRouteMethodException();
	}
}