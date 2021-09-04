<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Collection;

use ArrayAccess;
use Closure;
use Countable;
use phpDocumentor\Reflection\Types\Boolean;
use function array_key_exists;
use function array_pad;
use function array_pop;
use function array_reverse;
use function array_search;
use function array_shift;
use function array_unshift;
use function call_user_func;
use function count;
use function in_array;
use function is_double;
use function is_iterable;
use function mb_strtoupper;
use function range;
use function shuffle;
use function var_dump;

/**
 * Class Collection
 * @package Kikopolis\Core\Collection
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
abstract class Collection implements Countable, ArrayAccess {
	private array   $elements = [];
	private ?string $type;
	
	/**
	 * Pass a string from a child collection class to enforce types to this particular object.
	 * If this is not null, only objects of this type can be added to the collection.
	 */
	protected function __construct(string $type = null, array $elements = []) {
		$this->type = $type;
		$this->collect($elements);
	}
	
	final public static function make(array $elements, ?string $type = null): Collection {
		return new static($type, $elements);
	}
	
	final public static function integerRange(int $from, int $to): Collection {
		return new static(range($from, $to));
	}
	
	final public static function floatRange(float $from, float $to, int|float $step = 0.1): Collection {
		return new static(range($from, $to, $step));
	}
	
	final public static function alphabetRange(string $from, string $to, bool $capitalize = false): Collection {
		$elements = range($from, $to);
		if ($capitalize === true) {
			foreach ($elements as $key => $value) {
				$elements[$key] = mb_strtoupper($value);
			}
		}
		return new static($elements);
	}
	
	final public function collect(...$elements): Collection {
		foreach ($elements as $key1 => $element1) {
			if (is_iterable($element1)) {
				foreach ($element1 as $key2 => $element2) {
					$this->put($key2, $element2);
				}
			} else {
				$this->put($key1, $element1);
			}
		}
		return $this;
	}
	
	final public function put(int|string $key, mixed $element): Collection {
		if ($this->type !== null) {
			if (! $this->validateType($element)) {
				return $this;
			}
		}
		$this->elements[$key] = $element;
		return $this;
	}
	
	final public function prepend(mixed $element): Collection {
		array_unshift($this->elements, $element);
		return $this;
	}
	
	final public function push(mixed $element): Collection {
		if ($this->type !== null) {
			if (! $this->validateType($element)) {
				return $this;
			}
		}
		$this->elements[] = $element;
		return $this;
	}
	
	final public function padStart(int $itemsToAdd, mixed $value): Collection {
		if ($itemsToAdd > 0) {
			$itemsToAdd = ($this->count() + $itemsToAdd) * -1;
		} else {
			$itemsToAdd -= $this->count();
		}
		$this->elements = array_pad($this->elements, $itemsToAdd, $value);
		return $this;
	}
	
	final public function padToLengthStart(int $length, mixed $value): Collection {
		if ($length > 0) {
			$length *= -1;
		}
		$this->elements = array_pad($this->elements, $length, $value);
		return $this;
	}
	
	final public function padEnd(int $itemsToAdd, mixed $value): Collection {
		if ($itemsToAdd < 0) {
			$itemsToAdd = $this->count() + $itemsToAdd * -1;
		} else {
			$itemsToAdd += $this->count();
		}
		$this->elements = array_pad($this->elements, $itemsToAdd, $value);
		return $this;
	}
	
	final public function padToLengthEnd(int $length, mixed $value): Collection {
		if ($length < 0) {
			$length *= -1;
		}
		$this->elements = array_pad($this->elements, $length, $value);
		return $this;
	}
	
	final public function containsKey(int|string $key): bool {
		return array_key_exists($key, $this->elements);
	}
	
	final public function containsElement(mixed $element): bool {
		return in_array($element, $this->elements, true);
	}
	
	final public function empty(): bool {
		return empty($this->elements);
	}
	
	final public function all(): array {
		return $this->toArray();
	}
	
	final public function pop(int $count = 1): Collection {
		if ($this->empty()) {
			return new static($this->type, []);
		}
		if ($count === 1) {
			return new static($this->type, [array_pop($this->elements)]);
		}
		if ($count >= $this->count()) {
			return new static($this->type, $this->elements);
		}
		$results = [];
		for ($i = 0; $i < $count; $i++) {
			$results[] = array_pop($this->elements);
		}
		return new static($this->type, $results);
	}
	
	final public function shift(int $count = 1): Collection {
		if ($this->empty()) {
			return new static($this->type, []);
		}
		if ($count === 1) {
			return new static($this->type, [array_shift($this->elements)]);
		}
		if ($count >= $this->count()) {
			return new static($this->type, $this->elements);
		}
		$results = [];
		for ($i = 0; $i < $count; $i++) {
			$results[] = array_shift($this->elements);
		}
		return new static($this->type, $results);
	}
	
	final public function pull(int|string $key): mixed {
		if ($this->containsKey($key)) {
			$item = $this->get($key);
			$this->remove($key);
			return $item;
		}
		return null;
	}
	
	final public function get(int|string $key): mixed {
		return $this->containsKey($key) ? $this->elements[$key] : null;
	}
	
	final public function getKey(mixed $element): null|int|string {
		return $this->containsElement($element) ? array_search($element, $this->elements, true) : null;
	}
	
	final public function remove(int|string $key): Collection {
		if ($this->containsKey($key)) {
			unset($this->elements[$key]);
		}
		return $this;
	}
	
	final public function removeElement(mixed $element): Collection {
		if ($this->containsElement($element)) {
			$this->remove($this->getKey($element));
		}
		return $this;
	}
	
	final public function removeWhere(Closure $callback): Collection {
		foreach ($this->elements as $key => $element) {
			if ($callback($element)) {
				$this->remove($key);
			}
		}
		return $this;
	}
	
	final public function each(Closure $callback): bool {
		foreach ($this->elements as $key => $element) {
			if ($callback($element, $key) === false) {
				return false;
			}
		}
		return true;
	}
	
	final public function transform(Closure $callback): Collection {
		foreach ($this->elements as $key => $element) {
			$this->elements[$key] = $callback($element, $key);
		}
		return $this;
	}
	
	final public function first(?Closure $callback = null): mixed {
		if ($this->count() <= 0) {
			return null;
		}
		$elements = $this->elements;
		$last     = array_shift($elements);
		if ($callback !== null) {
			return $callback($last);
		}
		return $last;
	}
	
	final public function last(?Closure $callback = null): mixed {
		if ($this->count() <= 0) {
			return null;
		}
		$elements = $this->elements;
		$last     = array_pop($elements);
		if ($callback !== null) {
			return $callback($last);
		}
		return $last;
	}
	
	final public function shuffle(): Collection {
		shuffle($this->elements);
		return $this;
	}
	
	final public function reverse(): Collection {
		$this->elements = array_reverse($this->elements, true);
		return $this;
	}
	
	final public function filter(Closure $filter): Collection {
		foreach ($this->elements as $key => $element) {
			if ($filter($element, $key) === true) {
				$this->remove($key);
			}
		}
		return $this;
	}
	
	final public function filterKeys(...$keys): Collection {
		foreach ($keys as $key) {
			if ($this->containsKey($key)) {
				$this->remove($key);
			}
		}
		return $this;
	}
	
	final public function clear(): Collection {
		$this->elements = [];
		return $this;
	}
	
	final public function reset(): Collection {
		$this->elements = array_values($this->elements);
		return $this;
	}
	
	final public function count(): int {
		return count($this->elements);
	}
	
	public function toJson(int $flags = 0, int $depth = 512): string {
		return (string) json_encode($this->elements, $flags, $depth);
	}
	
	public function __toString(): string {
		return $this->toJson();
	}
	
	public function toString(): string {
		return $this->toJson();
	}
	
	public function toArray(): array {
		return $this->elements;
	}
	
	public function offsetExists($offset): bool {
		return $this->containsKey($offset);
	}
	
	public function offsetGet($offset): mixed {
		return $this->get($offset);
	}
	
	public function offsetSet($offset, $value): void {
		$this->put($offset, $value);
	}
	
	public function offsetUnset($offset): void {
		$this->remove($offset);
	}
	
	final protected function validateType(mixed $element): bool {
		if ($this->type === null) {
			return true;
		}
		return match ($this->type) {
			'int', 'integer' => is_int($element),
			'float' => is_float($element),
			'string' => is_string($element),
			default => $element instanceof $this->type,
		};
	}
}