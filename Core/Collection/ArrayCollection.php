<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Collection;

final class ArrayCollection extends Collection {
	public function __construct(array $elements = []) {
		parent::__construct('array', $elements);
	}
}