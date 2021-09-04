<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Router;

use Kikopolis\Core\Collection\Collection;

final class RouteCollection extends Collection {
	public function __construct() {
		parent::__construct(Route::class);
	}
}