<?php

declare(strict_types = 1);

namespace Kikopolis\Support;

use function is_iterable;

final class Num {
	public static function integers(...$numbers): bool {
		foreach ($numbers as $number) {
			if (is_iterable($number)) {
				foreach ($number as $value) {
					if (! is_int($value)) {
						return false;
					}
				}
			} else {
				if (! is_int($number)) {
					return false;
				}
			}
		}
		return true;
	}
}