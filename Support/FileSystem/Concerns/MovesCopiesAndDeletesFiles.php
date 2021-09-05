<?php

declare(strict_types = 1);

namespace Kikopolis\Support\FileSystem\Concerns;

use Kikopolis\Support\Exception\FileNotFoundException;
use Kikopolis\Support\FileSystem;
use function escapeshellarg;
use function exec;
use function rename;
use function sprintf;
use function str_contains;
use function symlink;
use function unlink;
use const PHP_OS;

trait MovesCopiesAndDeletesFiles {
	public static function delete(string $path): bool {
		if (FileSystem::exists($path)) {
			return unlink($path);
		}
		return false;
	}
	
	public static function move(string $from, string $to): bool {
		if (! FileSystem::exists($from)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $from));
		}
		return rename($from, $to);
	}
	
	public static function copy(string $from, string $to): bool {
		if (! FileSystem::exists($from)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $from));
		}
		return static::copy($from, $to);
	}
	
	public static function link(string $from, string $to): bool {
		if (! FileSystem::exists($from)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $from));
		}
		if (! str_contains(PHP_OS, 'WIN')) {
			return symlink($from, $to);
		}
		// 'j' for creating a symbolic directory link and 'h' for a hard link if dealing with a file
		return exec(sprintf('mklink /%s %s %s', FileSystem::isDirectory($from) ? 'j' : 'h', escapeshellarg($to), escapeshellarg($from)));
	}
}