<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Cache;

use Kikopolis\Core\Application;
use Kikopolis\Support\FileSystem;
use Kikopolis\Support\Str;

class Cache {
	private string $cacheDir;
	private string $cacheKey;
	private int    $lifeTime;
	
	/**
	 * @param  string|null  $cacheDir
	 * @param  int          $lifeTime  The lifetime of the cache in seconds
	 *                                 todo currently no "forever" setting
	 */
	public function __construct(string $cacheDir = null, int $lifeTime = 3) {
		// Lifetime is used in the expired calculations so this should be one of the first initializations
		$this->lifeTime = $lifeTime;
		$this->cacheDir = $cacheDir
			?: sprintf(
				'%s%svar%scache%s',
				Application::getProjectPath(),
				DIRECTORY_SEPARATOR,
				DIRECTORY_SEPARATOR,
				DIRECTORY_SEPARATOR
			);
		$this->setCacheKey();
	}
	
	public function composeName(string $templateName): string {
		return sprintf("%s_%s", $this->cacheKey, Str::parseDot($templateName, '_'));
	}
	
	public function getCacheDir(): string {
		return $this->cacheDir;
	}
	
	private function cleanCacheDir(): void {
		$files = glob($this->cacheDir . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
	}
	
	private function isExpired(string $cacheKey): bool {
		return time() > (int) $cacheKey + $this->lifeTime;
	}
	
	private function setCacheKey(): void {
		$cacheKeyFilename = sprintf(
			"%s%svar%scache%s.key",
			Application::getProjectPath(),
			DIRECTORY_SEPARATOR,
			DIRECTORY_SEPARATOR,
			DIRECTORY_SEPARATOR
		);
		if (FileSystem::isFile($cacheKeyFilename)) {
			$cacheKey = FileSystem::contents($cacheKeyFilename);
		}
		if (! isset($cacheKey) || $this->isExpired($cacheKey)) {
			$this->cleanCacheDir();
			$cacheKey = (string) time();
			FileSystem::write($cacheKeyFilename, $cacheKey);
		}
		$this->cacheKey = $cacheKey;
	}
}
