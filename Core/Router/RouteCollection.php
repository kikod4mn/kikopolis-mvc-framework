<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Router;

use Kikopolis\Core\Collection\Collection;

/**
 * Class RouteCollection
 * @package Kikopolis\Core\Router
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
final class RouteCollection extends Collection {
	public function __construct() {
		parent::__construct(Route::class);
	}
}