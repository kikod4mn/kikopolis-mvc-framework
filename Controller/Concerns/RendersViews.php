<?php

declare(strict_types = 1);

namespace Kikopolis\Controller\Concerns;

use Kikopolis\Core\Application;
use Kikopolis\Core\Response;

trait RendersViews {
	protected function render(string $view, array $params = []): Response {
		return new Response(200, Application::getApp()->getView()->render($view, $params));
	}
}