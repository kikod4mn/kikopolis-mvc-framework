<?php

declare(strict_types = 1);

namespace Kikopolis\Support\FileSystem\Concerns;

use Kikopolis\Support\Exception\FileNotFoundException;
use Kikopolis\Support\FileSystem;
use function file_get_contents;
use function file_put_contents;
use function sprintf;
use const FILE_APPEND;
use const LOCK_EX;

trait ReadsFromAndWritesToFiles {
	public static function contents(string $path): string {
		if (! FileSystem::isFile($path) || ! FileSystem::exists($path)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $path));
		}
		return file_get_contents($path);
	}
	
	public static function write(string $path, string $content): bool {
		return file_put_contents($path, $content, LOCK_EX);
	}
	
	public static function append(string $path, string $content): bool {
		if (FileSystem::exists($path)) {
			return file_put_contents($path, $content, FILE_APPEND);
		}
		return false;
	}
	
	public static function prepend(string $path, string $content): bool {
		if (FileSystem::exists($path)) {
			$prevContent = static::contents($path);
			return file_put_contents($path, sprintf("%s%s", $content, $prevContent));
		}
		return false;
	}
}