<?php

declare(strict_types = 1);

namespace Kikopolis\Support;

final class Arr {
	/**
	 * Returns true if the array is associative but does not have sequential numeric keys, ex - 1, 2, 0 - will return as associative.
	 * @param   array   $array
	 * @return bool
	 */
	public static function associative(array $array): bool {
		$keys = array_keys($array);
		return array_keys($keys) === $keys || Num::integers($keys);
	}
}