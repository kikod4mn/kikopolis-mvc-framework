<?php

declare(strict_types = 1);

if (! function_exists('dd')) {
	function dd(...$vars): void {
		foreach ($vars as $var) {
			echo "<pre>";
			var_dump($var);
			echo "</pre>";
		}
		die;
	}
}