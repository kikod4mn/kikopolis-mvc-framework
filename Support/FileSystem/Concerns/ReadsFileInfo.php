<?php

declare(strict_types = 1);

namespace Kikopolis\Support\FileSystem\Concerns;

use Kikopolis\Support\Exception\FileNotFoundException;
use Kikopolis\Support\FileSystem;
use function filesize;
use function finfo_file;
use function finfo_open;
use function pathinfo;
use function pow;
use function round;
use function sprintf;
use const FILEINFO_MIME_TYPE;
use const PATHINFO_BASENAME;
use const PATHINFO_DIRNAME;
use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;

trait ReadsFileInfo {
	public static function name(string $path): string {
		if (! FileSystem::exists($path)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $path));
		}
		return pathinfo($path, PATHINFO_FILENAME);
	}
	
	public static function basename($path): string {
		if (! FileSystem::exists($path)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $path));
		}
		return pathinfo($path, PATHINFO_BASENAME);
	}
	
	public static function dirname(string $path): string {
		if (! FileSystem::exists($path)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $path));
		}
		return pathinfo($path, PATHINFO_DIRNAME);
	}
	
	public static function extension(string $path): string {
		if (! FileSystem::exists($path)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $path));
		}
		return pathinfo($path, PATHINFO_EXTENSION);
	}
	
	public static function mimetype(string $path): string {
		if (! FileSystem::exists($path)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $path));
		}
		return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
	}
	
	public static function size(string $path, string $unit = 'byte', int $precision = 2): string {
		if (! FileSystem::exists($path)) {
			throw new FileNotFoundException(sprintf('File "%s" does not exist.', $path));
		}
		$size = filesize($path);
		return match ($unit) {
			'tb', 'TB' => sprintf("%d TB", round($size / pow(1024, 4), $precision)),
			'gb', 'GB' => sprintf("%d GB", round($size / pow(1024, 3), $precision)),
			'mb', 'MB' => sprintf("%d MB", round($size / pow(1024, 2), $precision)),
			'kb', 'KB' => sprintf("%d MB", round($size / 1024, $precision)),
			default => sprintf("%d bytes", (string) $size),
		};
	}
}