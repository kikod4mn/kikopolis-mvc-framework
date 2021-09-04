<?php

declare(strict_types = 1);

namespace Kikopolis\View;

use Kikopolis\Core\Application;
use Kikopolis\Core\Contracts\RendererInterface;
use Kikopolis\View\Exception\ParentTemplateCannotContainMergeDirectiveException;
use Kikopolis\View\Exception\TemplateDoesNotExistException;
use Kikopolis\View\Exception\TooManyMergeDirectivesException;
use function count;
use function file_exists;
use function file_get_contents;
use function preg_match;
use function preg_match_all;
use function preg_quote;
use function preg_replace;
use function sprintf;
use function str_replace;
use const DIRECTORY_SEPARATOR;
use const PREG_SET_ORDER;

final class View implements RendererInterface {
	private string $mergeDirective   = '/^@merge\((?P<template>[a-z\.]+)\)\s+?/';
	private string $contentDirective = '/{{\s*?@content\s*?}}/';
	private string $titleDirective   = '/{{\s*?@title\((?P<title>[\w+]+)\)\s*?}}/';
	private string $file;
	
	public function file(string $file): self {
		$this->file = $this->checkFileExists(sprintf("%s/resources/views/%s.php", Application::getProjectPath(), $file));
		return $this;
	}
	
	public function render(?string $content = null): string {
		if ($content === null && isset($this->file)) {
			$content = file_get_contents($this->file);
		}
		preg_match_all($this->mergeDirective, $content, $mergeMatches, PREG_SET_ORDER);
		if ($mergeMatches) {
			$content = $this->mergeWithBase($content, $mergeMatches);
		}
		return $content;
	}
	
	// todo write a separate file loader so it would be possible to unit test View
	private function mergeWithBase(string $content, array $mergeMatches): string {
		if (count($mergeMatches) > 1) {
			throw new TooManyMergeDirectivesException($this->file);
		}
		$parentFile     = $this->checkFileExists(
			sprintf("%s/resources/views/%s.php", Application::getProjectPath(), $this->parseDot($mergeMatches[0]['template']))
		);
		$content        = preg_replace($this->mergeDirective, '', $content);
		$parentContents = file_get_contents($parentFile);
		if (preg_match($this->mergeDirective, $parentContents)) {
			throw new ParentTemplateCannotContainMergeDirectiveException($parentFile);
		}
		$content = preg_replace($this->contentDirective, $content, $parentContents, 1);
		return preg_replace($this->contentDirective, '', $content, 0);
	}
	
	private function checkFileExists(string $file): string {
		if (! file_exists($file)) {
			throw new TemplateDoesNotExistException($file);
		}
		return $file;
	}
	
	private function parseDot(string $raw): string {
		return str_replace('.', DIRECTORY_SEPARATOR, $raw);
	}
}