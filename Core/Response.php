<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use function http_response_code;

final class Response {
	private int $code;
	
	public function getStatusCode(): int {
		return $this->code;
	}
	
	public function setStatusCode(int $code): void {
		$this->code = $code;
		http_response_code($code);
	}
}