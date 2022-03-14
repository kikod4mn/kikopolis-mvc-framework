<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use FilesystemIterator;
use Kikopolis\Core\Collection\Collection;
use Kikopolis\Core\Collection\MixedCollection;
use Kikopolis\Core\Collection\StringCollection;
use Kikopolis\Support\FileSystem;
use function array_merge;
use function class_exists;
use function dd;
use function mb_strcut;
use function mb_strpos;
use function sprintf;
use function str_contains;
use function str_replace;
use function trim;
use const DIRECTORY_SEPARATOR;

final class Config {
	private static Collection $services;
	
	public function services(): array {
		if (isset(self::$services) === false) {
			$configured     = require_once Application::getProjectPath() . '/config/services.php';
			$controllers    = $this->loadControllers(sprintf("%s%s%s", Application::getProjectPath(), DIRECTORY_SEPARATOR, 'Controller'));
			self::$services = new StringCollection(array_merge($controllers, $configured));
		}
		return self::$services->toArray();
	}
	
	public function loadRoutes(): void {
		require_once Application::getProjectPath() . '/routes/routes.php';
	}
	
	public static function self(): Config {
		return new self();
	}
	
	public static function controllerNamespace(): string {
		return 'App\\Controller\\';
	}
	
	private function loadControllers(string $path): array {
		$loaded = [];
		$files  = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
		foreach ($files as $file) {
			$pathname = $file->getPathname();
			if (str_contains($file->getBasename(), 'Controller') === true
				&& str_contains($file->getBasename(), '.php') === true
				&& class_exists('App\\' . mb_strcut($pathname, mb_strpos($pathname, 'Controller'))) === false
			) {
				$classname = str_replace('.php', '', 'App\\' . mb_strcut($pathname, mb_strpos($pathname, 'Controller')));
//				if (class_exists($classname) === false) {
//					require_once $pathname;
//				}
				$loaded[$classname] = $classname;
			}
			if (FileSystem::isDirectory($pathname)) {
				$loaded = array_merge($loaded, $this->loadControllers($pathname));
			}
		}
		return $loaded;
	}
}