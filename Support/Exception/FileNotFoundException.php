<?php

declare(strict_types = 1);

namespace Kikopolis\Support\Exception;

use Exception;
use Throwable;

class FileNotFoundException extends Exception {
	public function __construct($message = "", $code = 0, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}