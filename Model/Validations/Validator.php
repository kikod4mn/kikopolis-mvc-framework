<?php

declare(strict_types = 1);

namespace Kikopolis\Model\Validations;

use Kikopolis\Core\Collection\ArrayCollection;
use function explode;

final class Validator {
	private ArrayCollection $rules;
	
	public function __construct(array $ruleStrings) {
		$this->rules = new ArrayCollection();
		$this->rules->collect(...$this->parseRules($ruleStrings));
	}
	
	private function parseRules(array $ruleStrings): array {
		return array_map(fn ($ruleString) => $this->parseRuleString($ruleString), $ruleStrings);
	}
	
	private function parseRuleString(string $ruleString): array {
		$parsed = explode('|', $ruleString);
		foreach ($parsed as $propName => $propValue) {
			if (str_contains($propValue, ':')) {
				$parsed[$propName] = explode(':', $propValue);
			}
		}
		return $parsed;
	}
}