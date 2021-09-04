<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Collection;

class MixedCollection extends Collection {
	public function __construct(?string $type = null, array $elements = []) {
		parent::__construct($type, $elements);
	}
}