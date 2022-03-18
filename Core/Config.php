<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use FilesystemIterator;
use Kikopolis\Core\Collection\Collection;
use Kikopolis\Core\Collection\StringCollection;
use Kikopolis\Support\FileSystem;
use function array_merge;
use function class_exists;
use function mb_strcut;
use function mb_strpos;
use function sprintf;
use function str_contains;
use function str_replace;
use const DIRECTORY_SEPARATOR;

final class Config {
	public const ENVIRONMENT = 'environment';
	public const DEBUG       = 'debug';
	
	private Collection $services;
	private array      $config;
	
	public function __construct() {
		$this->config = require sprintf("%s%sconfig%sconfig.php", Application::getProjectPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
	}
	
	public function services(): array {
		if (isset($this->services) === false) {
			$configured     = require sprintf("%s/config/services.php", Application::getProjectPath());
			$controllers    = $this->loadControllers(sprintf("%s%s%s", Application::getProjectPath(), DIRECTORY_SEPARATOR, 'Controller'));
			$this->services = new StringCollection(array_merge($controllers, $configured));
		}
		return $this->services->toArray();
	}
	
	public function loadRoutes(): void {
		require_once sprintf("%s%sroutes%sroutes.php", Application::getProjectPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
	}
	
	public static function controllerNamespace(): string {
		return 'App\\Controller\\';
	}
	
	public function isDevelopment(): bool {
		return array_key_exists(self::ENVIRONMENT, $this->config) && $this->config[self::ENVIRONMENT] === true;
	}
	
	public function displayErrors(): bool {
		return array_key_exists(self::DEBUG, $this->config) && $this->config[self::DEBUG] === true;
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
				$classname = str_replace(['.php', '/'], ['', '\\'], sprintf('App\%s', mb_strcut($pathname, mb_strpos($pathname, 'Controller'))));
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
