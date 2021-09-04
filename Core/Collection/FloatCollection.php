<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Collection;

class FloatCollection extends Collection {
	public function __construct(array $elements = []) {
		parent::__construct('float', $elements);
	}
}