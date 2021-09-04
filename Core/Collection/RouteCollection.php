<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Collection;

use Kikopolis\Core\Router\Route;

final class RouteCollection extends Collection {
	public function __construct() {
		parent::__construct(Route::class);
	}
}