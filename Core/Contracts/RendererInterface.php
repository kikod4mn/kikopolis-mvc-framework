<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Contracts;

interface RendererInterface {
	public function render(?string $content = null): string;
}