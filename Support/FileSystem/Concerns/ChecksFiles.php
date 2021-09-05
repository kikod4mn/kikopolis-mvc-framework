<?php

declare(strict_types = 1);

namespace Kikopolis\Support\FileSystem\Concerns;

use function file_exists;
use function is_dir;
use function is_file;
use function is_readable;
use function is_writable;

trait ChecksFiles {
	public static function exists(string $path): bool {
		return file_exists($path);
	}
	
	public static function isFile(string $path): bool {
		return is_file($path);
	}
	
	public static function isDirectory(string $path): bool {
		return is_dir($path);
	}
	
	public static function isWritable(string $path): bool {
		return is_writable($path);
	}
	
	public static function isReadable(string $path): bool {
		return is_readable($path);
	}
}