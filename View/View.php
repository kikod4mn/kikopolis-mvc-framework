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
use function trim;
use function var_dump;
use const DIRECTORY_SEPARATOR;
use const PREG_SET_ORDER;

final class View implements RendererInterface {
	private string             $mergeDirective   = '/^@merge\((?P<template>[a-z\.]+)\)\s+?/';
	private string             $contentDirective = '/{{\s*?@content\s*?}}/';
	private string             $titleDirective   = '/{{\s*?@title\((?P<title>[\w+]+)\)\s*?}}/';
	private TemplateFileLoader $templateLoader;
	
	public function __construct(TemplateFileLoader $templateLoader) {
		$this->templateLoader = $templateLoader;
	}
	
	public function render(string $name): string {
		$content = $this->templateLoader->get($name);
		preg_match_all($this->mergeDirective, $content, $mergeMatches, PREG_SET_ORDER);
		if (count($mergeMatches) > 0) {
			if (count($mergeMatches) > 1) {
				throw new TooManyMergeDirectivesException($this->formatFullName($name));
			}
			$content = $this->mergeWithBase($content, $mergeMatches);
		}
		return $content;
	}
	
	// todo write a separate file loader so it would be possible to unit test View
	private function mergeWithBase(string $content, array $mergeMatches): string {
		$content        = trim(preg_replace($this->mergeDirective, '', $content));
		$parentContents = $this->templateLoader->get($mergeMatches[0]['template']);
		if (preg_match($this->mergeDirective, $parentContents)) {
			throw new ParentTemplateCannotContainMergeDirectiveException($this->formatFullName($mergeMatches[0]['template']));
		}
		$merged = preg_replace($this->contentDirective, $content, $parentContents, 1);
		return preg_replace($this->contentDirective, '', $merged, 0);
	}
	
	private function formatFullName(string $name): string {
		return sprintf("%s%s%s", $this->templateLoader->getBaseDir(), DIRECTORY_SEPARATOR, $this->templateLoader->parseDot($name));
	}
}