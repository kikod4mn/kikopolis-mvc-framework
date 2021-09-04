<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Collection;

class IntegerCollection extends Collection {
	public function __construct(array $elements = []) {
		parent::__construct('integer', $elements);
	}
}