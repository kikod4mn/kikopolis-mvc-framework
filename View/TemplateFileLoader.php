<?php

declare(strict_types = 1);

namespace Kikopolis\View;

use Kikopolis\Core\Application;
use Kikopolis\Core\Cache\Cache;
use Kikopolis\Support\FileSystem;
use Kikopolis\Support\Str;
use Kikopolis\View\Exception\TemplateDoesNotExistException;
use function sprintf;
use function str_replace;
use const DIRECTORY_SEPARATOR;

/**
 * todo lessen knowledge of folder structures in the cache
 */
final class TemplateFileLoader {
	public function __construct(private Cache $cache) {}
	
	public function loadTemplate(string $name): string {
		$path = sprintf("%s%s%s.php", $this->getBaseDir(), DIRECTORY_SEPARATOR, $this->parseDot($name));
		if (! FileSystem::isFile($path) || ! FileSystem::isReadable($path)) {
			throw new TemplateDoesNotExistException($path);
		}
		return FileSystem::contents($path);
	}
	
	public function isCached(string $templateName): bool {
		return FileSystem::exists($this->getFullCacheFilename($templateName));
	}
	
	public function getCached(string $templateName): string {
		return $this->getFullCacheFilename($templateName);
	}
	
	public function writeToCache(string $templateName, string $content): string {
		$cacheFilename = $this->getFullCacheFilename($templateName);
		FileSystem::write($cacheFilename, $content);
		return $cacheFilename;
	}
	
	public function getTemplatePath(string $templateName): string {
		return sprintf("%s%s%s.php", $this->getBaseDir(), DIRECTORY_SEPARATOR, $this->parseDot($templateName));
	}
	
	private function parseDot(string $raw): string {
		return str_replace('.', DIRECTORY_SEPARATOR, $raw);
	}
	
	private function getBaseDir(): string {
		return sprintf("%s%sresources%sviews", Application::getProjectPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
	}
	
	private function getFullCacheFilename(string $templateName): string {
		return sprintf("%s%s", $this->cache->getCacheDir(), $this->getCacheFilename($templateName));
	}
	
	private function getCacheFilename(string $templateName): string {
		return sprintf(
			"%s.php",
			$this->cache->composeName(
				Str::parseDot($templateName, '/')
			)
		);
	}
}
