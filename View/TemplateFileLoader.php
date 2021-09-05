<?php

declare(strict_types = 1);

namespace Kikopolis\View;

use Kikopolis\Core\Application;
use Kikopolis\Support\FileSystem\Concerns\ChecksFiles;
use Kikopolis\Support\FileSystem\Concerns\ReadsFromAndWritesToFiles;
use Kikopolis\View\Exception\TemplateDoesNotExistException;
use function sprintf;
use function str_replace;
use const DIRECTORY_SEPARATOR;

final class TemplateFileLoader {
	use ChecksFiles;
	use ReadsFromAndWritesToFiles;
	
	public function get(string $name): string {
		$path = sprintf("%s%s%s.php", $this->getBaseDir(), DIRECTORY_SEPARATOR, $this->parseDot($name));
		if (! TemplateFileLoader::isFile($path) || ! TemplateFileLoader::isReadable($path)) {
			throw new TemplateDoesNotExistException($path);
		}
		return TemplateFileLoader::contents($path);
	}
	
	public function parseDot(string $raw): string {
		return str_replace('.', DIRECTORY_SEPARATOR, $raw);
	}
	
	public function getBaseDir(): string {
		return sprintf("%s%sresources%sviews", Application::getProjectPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
	}
}