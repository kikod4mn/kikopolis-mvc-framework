<?php

declare(strict_types = 1);

namespace Kikopolis\Core\Collection;

use ArrayAccess;
use Closure;
use Countable;
use function array_key_exists;
use function array_search;
use function count;
use function in_array;
use function var_dump;

/**
 * Class Collection
 * @package Kikopolis\Core\Collection
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class Collection implements Countable, ArrayAccess {
	private array $items = [];
	
	private ?string $type;
	
	/**
	 * Pass a string from a child collection class to enforce types to this particular object.
	 * If this is not null, only objects of this type can be added to the collection.
	 */
	protected function __construct(string $type = null) {
		$this->type = $type;
	}
	
	final public function exists(int|string $key): bool {
		return array_key_exists($key, $this->items);
	}
	
	final public function contains(mixed $value): bool {
		return in_array($value, $this->items);
	}
	
	final public function empty(): bool {
		return empty($this->items);
	}
	
	final public function all(): array {
		return $this->toArray();
	}
	
	final public function get(int|string $key): mixed {
		return $this->exists($key) ? $this->items[$key] : null;
	}
	
	final public function key(mixed $value): null|int|string {
		return $this->contains($value) ? array_search($value, $this->items) : null;
	}
	
	final public function add(int|string $key, mixed $value): void {
		if ($this->type !== null) {
			if (! $this->validateType($value)) {
				return;
			}
		}
		$this->items[$key] = $value;
	}
	
	final public function remove(int|string $key): void {
		if ($this->exists($key)) {
			unset($this->items[$key]);
		}
	}
	
	public function each(Closure $callback): bool {
		foreach ($this->items as $key => $value) {
			if ($callback($value, $key) === false) {
				return false;
			}
		}
		return true;
	}
	
	final public function count(): int {
		return count($this->items);
	}
	
	final protected function validateType(mixed $value): bool {
		return $value instanceof $this->type;
	}
	
	public function toArray(): array {
		return $this->items;
	}
	
	public function offsetExists($offset): bool {
		return $this->exists($offset);
	}
	
	public function offsetGet($offset): mixed {
		return $this->get($offset);
	}
	
	public function offsetSet($offset, $value): void {
		$this->add($offset, $value);
	}
	
	public function offsetUnset($offset): void {
		$this->remove($offset);
	}
}