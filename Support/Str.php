<?php

declare(strict_types = 1);

namespace Kikopolis\Support;

use function str_replace;

class Str {
	public static function parseDot(string $subject, string $replace): string {
		return str_replace('.', $replace, $subject);
	}
}
