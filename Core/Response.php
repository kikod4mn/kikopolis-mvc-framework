<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use function http_response_code;

final class Response {
	private int    $code;
	private string $content;
	
	public function __construct(int $code = 200, string $content = '') {
		$this->code    = $code;
		$this->content = $content;
	}
	
	public function getStatusCode(): int {
		return $this->code;
	}
	
	public function setStatusCode(int $code): Response {
		$this->code = $code;
		http_response_code($code);
		return $this;
	}
	
	public function getContent(): string {
		return $this->content;
	}
	
	public function setContent(string $content): Response {
		$this->content = $content;
		return $this;
	}
}