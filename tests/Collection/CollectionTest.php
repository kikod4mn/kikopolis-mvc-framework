<?php

declare(strict_types = 1);

namespace Tests\Collection;

use Closure;
use Generator;
use Kikopolis\Core\Collection\Collection;
use Kikopolis\Core\Collection\FloatCollection;
use Kikopolis\Core\Collection\IntegerCollection;
use Kikopolis\Core\Collection\MixedCollection;
use Kikopolis\Core\Collection\StringCollection;
use Kikopolis\Core\Router\Route;
use PHPUnit\Framework\TestCase;
use function array_values;
use function count;

final class CollectionTest extends TestCase {
	/**
	 * Define a random class to use as a test for the collection type system.
	 * Any class will do as constructor is disabled.
	 * @var string
	 */
	private string $mockableObject = Route::class;
	
	public function testMake(): void {
		$arr = ['a', 1.2, 44, $this->getObjectMock()];
		CollectionTest::assertEquals($arr, MixedCollection::make($arr)->all());
	}
	
	public function testIntegerRange(): void {
		CollectionTest::assertEquals([0, 1, 2, 3, 4], IntegerCollection::integerRange(0, 4)->all());
	}
	
	public function testFloatRange(): void {
		CollectionTest::assertEquals([1, 1.1, 1.2, 1.3, 1.4, 1.5], FloatCollection::floatRange(1, 1.5)->all());
	}
	
	public function testAlphabetRange(): void {
		CollectionTest::assertEquals(['a', 'b', 'c'], StringCollection::alphabetRange('a', 'c')->all());
	}
	
	/**
	 * @dataProvider collectProvider
	 */
	public function testCollect(array $items, int $count): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertEquals($count, $collection->count());
	}
	
	public function collectProvider(): Generator {
		yield [
			'items' => [$this->getObjectMock(), $this->getObjectMock(), $this->getObjectMock()],
			'count' => 3,
		];
		yield [
			'items' => [1, 23, 41, 'asd', true],
			'count' => 5,
		];
	}
	
	public function testPut(): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect('asd', 'dsa');
		$collection->put(2, 1);
		CollectionTest::assertTrue($collection->containsElement(1));
		CollectionTest::assertTrue($collection->containsKey(2));
	}
	
	public function testPrepend(): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect('asd', 'dsa');
		$collection->prepend(1);
		CollectionTest::assertTrue($collection->containsElement(1));
	}
	
	public function testPush(): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect('asd', 'dsa');
		$collection->push(1);
		CollectionTest::assertTrue($collection->containsElement(1));
	}
	
	/**
	 * @dataProvider padProvider
	 */
	public function testPadStart(int $lengthToAdd): void {
		$arr        = ['asd', 'dsa'];
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($arr);
		$collection->padStart($lengthToAdd, 'test');
		CollectionTest::assertEquals(4, $collection->count());
		CollectionTest::assertEquals(['test', 'test', ...$arr], $collection->all());
	}
	
	/**
	 * @dataProvider padToLengthProvider
	 */
	public function testPadToLengthStart(int $lengthToAdd): void {
		$arr        = ['asd', 'dsa'];
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($arr);
		$collection->padToLengthStart($lengthToAdd, 'test');
		CollectionTest::assertEquals(4, $collection->count());
		CollectionTest::assertEquals(['test', 'test', ...$arr], $collection->all());
	}
	
	/**
	 * @dataProvider padProvider
	 */
	public function testPadEnd(int $lengthToAdd): void {
		$arr        = ['asd', 'dsa'];
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($arr);
		$collection->padEnd($lengthToAdd, 'test');
		CollectionTest::assertEquals(4, $collection->count());
		CollectionTest::assertEquals([...$arr, 'test', 'test'], $collection->all());
	}
	
	/**
	 * @dataProvider padToLengthProvider
	 */
	public function testPadToLengthEnd(int $lengthToAdd): void {
		$arr        = ['asd', 'dsa'];
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($arr);
		$collection->padToLengthEnd($lengthToAdd, 'test');
		CollectionTest::assertEquals(4, $collection->count());
		CollectionTest::assertEquals([...$arr, 'test', 'test'], $collection->all());
	}
	
	public function padProvider(): Generator {
		yield ['lengthToAdd' => 2];
		yield ['lengthToAdd' => -2];
	}
	
	public function padToLengthProvider(): Generator {
		yield ['lengthToAdd' => 4];
		yield ['lengthToAdd' => -4];
	}
	
	/**
	 * @dataProvider allProvider
	 */
	public function testAll(?string $type, array $items, array $result): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertEquals($result, $collection->all());
	}
	
	public function allProvider(): Generator {
		yield [
			'type'   => null,
			'items'  => ['item1' => 1, 'item2' => 2, 'item3' => 3],
			'result' => ['item1' => 1, 'item2' => 2, 'item3' => 3],
		];
		yield [
			'type'   => $this->mockableObject,
			'items'  => ['item1' => 1, 'item2' => 2, 'item3' => $this->getObjectMock()],
			'result' => ['item3' => $this->getObjectMock()],
		];
	}
	
	/**
	 * @dataProvider popProvider
	 */
	public function testPop(array $items, array $expected, int $count): void {
		$collection = new MixedCollection();
		$collection->collect($items);
		$result = $collection->pop($count)->toArray();
		CollectionTest::assertEquals($expected, $result);
		CollectionTest::assertEquals(count($items) - $count, $collection->count());
	}
	
	public function popProvider(): Generator {
		yield [
			'items'    => [1, 2, 3],
			'expected' => [3, 2],
			'count'    => 2,
		];
		yield [
			'items'    => [1, 2, 3],
			'expected' => [3],
			'count'    => 1,
		];
	}
	
	/**
	 * @dataProvider shiftProvider
	 */
	public function testShift(array $items, array $expected, int $count): void {
		$collection = new MixedCollection();
		$collection->collect($items);
		$result = $collection->shift($count)->toArray();
		CollectionTest::assertEquals($expected, $result);
		CollectionTest::assertEquals(count($items) - $count, $collection->count());
	}
	
	public function shiftProvider(): Generator {
		yield [
			'items'    => [1, 2, 3],
			'expected' => [1, 2],
			'count'    => 2,
		];
		yield [
			'items'    => [1, 2, 3],
			'expected' => [1],
			'count'    => 1,
		];
	}
	
	public function testPull(): void {
		$items      = [1, 2, 3];
		$collection = new MixedCollection();
		$collection->collect($items);
		$result = $collection->pull(0);
		CollectionTest::assertEquals(1, $result);
		CollectionTest::assertEquals([2, 3], array_values($collection->all()));
	}
	
	/**
	 * @dataProvider containsKeyProvider
	 */
	public function testContainsKey(array $items, int|string $search): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertTrue($collection->containsKey($search));
	}
	
	public function containsKeyProvider(): Generator {
		yield [
			'items'  => ['key1' => 'value1', 'key2' => 'value2'],
			'search' => 'key2',
		];
		yield [
			'items'  => [4, 54, 12],
			'search' => 2,
		];
	}
	
	/**
	 * @dataProvider containsElementProvider
	 */
	public function testContainsElement(array $items, int|string|object $search): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertTrue($collection->containsElement($search));
	}
	
	public function containsElementProvider(): Generator {
		yield [
			'items'  => ['key1' => 'value1', 'key2' => 'value2'],
			'search' => 'value2',
		];
		yield [
			'items'  => [4, 54, 12],
			'search' => 54,
		];
		$search = $this->getObjectMock();
		yield [
			'items'  => [4, 54, 12, $search, 'string'],
			'search' => $search,
		];
	}
	
	/**
	 * @dataProvider emptyProvider
	 */
	public function testEmpty(array $items, bool $result): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertEquals($result, $collection->empty());
	}
	
	public function emptyProvider(): Generator {
		yield [
			'items'  => [
				'key1'   => 0,
				'value1' => $this->getObjectMock(),
				'key2'   => 1,
				'value2' => $this->getObjectMock(),
			],
			'result' => false,
		];
		yield [
			'items'  => [],
			'result' => true,
		];
	}
	
	public function testGet(): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect(['asd', 1, 3, 4, 55]);
		CollectionTest::assertEquals('asd', $collection->get(0));
	}
	
	/**
	 * @dataProvider getKeyProvider
	 */
	public function testGetKey($key, $value): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->put($key, $value);
		CollectionTest::assertEquals($key, $collection->getKey($value));
	}
	
	public function getKeyProvider(): Generator {
		yield [
			'key'   => 0,
			'value' => 'string',
		];
		yield [
			'key'   => 'key',
			'value' => 'value',
		];
		yield [
			'key'   => 0,
			'value' => $this->getObjectMock(),
		];
	}
	
	/**
	 * @dataProvider removeProvider
	 */
	public function testRemove($items, $removeKey): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertEquals($items, $collection->all());
		$collection->remove($removeKey);
		CollectionTest::assertFalse($collection->containsKey($removeKey));
	}
	
	public function removeProvider(): Generator {
		yield [
			'items'     => [1, 2, 3],
			'removeKey' => 0,
		];
		yield [
			'items'     => ['asd', 'dsa', 'afg', 'gfa'],
			'removeKey' => 1,
		];
		yield [
			'items'     => ['a' => true, 'b' => false],
			'removeKey' => 'a',
		];
	}
	
	/**
	 * @dataProvider removeElementProvider
	 */
	public function testRemoveElement($items, $toRemove): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertEquals($items, $collection->all());
		$collection->removeElement($toRemove);
		CollectionTest::assertFalse($collection->containsElement($toRemove));
	}
	
	public function removeElementProvider(): Generator {
		yield [
			'items'    => [1, 2, 3],
			'toRemove' => 3,
		];
		$obj = $this->getObjectMock();
		yield [
			'items'    => ['asd' => 1, 'ads' => new \stdClass(), 'class' => $obj],
			'toRemove' => $obj,
		];
	}
	
	/**
	 * @dataProvider removeWhereProvider
	 */
	public function testRemoveWhere($items, $result): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertEquals($items, $collection->all());
		$collection->removeWhere(fn ($item) => $item === 1);
		CollectionTest::assertEquals(array_values($result), array_values($collection->all()));
	}
	
	public function removeWhereProvider(): Generator {
		yield [
			'items'  => [1, 9],
			'result' => [9],
		];
		yield [
			'items'  => [1, 9, 1, 2, 1],
			'result' => [9, 2],
		];
	}
	
	/**
	 * @dataProvider eachProvider
	 */
	public function testTestEach($items, $result): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertEquals($result, $collection->each(fn ($item) => $item === $result));
	}
	
	public function eachProvider(): Generator {
		yield [
			'items'  => [0 => true, 1 => true, 2 => false],
			'result' => false,
		];
		yield [
			'items'  => [0 => true, 1 => true, 2 => true],
			'result' => true,
		];
	}
	
	/**
	 * @dataProvider modifyProvider
	 */
	public function testModify(array $items, array $result, Closure $callback): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		$collection->transform($callback);
		CollectionTest::assertEquals($result, $collection->all());
	}
	
	public function modifyProvider(): Generator {
		yield [
			'items'    => [0 => true, 1 => true, 2 => false],
			'result'   => [0 => false, 1 => false, 2 => true],
			'callback' => fn ($item) => ! ($item === true),
		];
		yield [
			'items'    => [0 => true, 1 => true, 2 => true],
			'result'   => [0 => false, 1 => false, 2 => false],
			'callback' => fn ($item) => ! ($item === true),
		];
	}
	
	/**
	 * @dataProvider firstProvider
	 */
	public function testFirst($items, $result): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		foreach ($items as $key => $value) {
			$collection->put($key, $value);
		}
		CollectionTest::assertEquals($result, $collection->first(fn ($last) => ++$last));
	}
	
	public function firstProvider(): Generator {
		yield [
			'items'  => [0 => 0, 1 => 1],
			'result' => 1,
		];
		yield [
			'items'  => [0 => 0],
			'result' => 1,
		];
		yield [
			'items'  => [],
			'result' => null,
		];
	}
	
	/**
	 * @dataProvider lastProvider
	 */
	public function testLast($items, $result): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		foreach ($items as $key => $value) {
			$collection->put($key, $value);
		}
		CollectionTest::assertEquals($result, $collection->last(fn ($last) => ++$last));
	}
	
	public function lastProvider(): Generator {
		yield [
			'items'  => [0 => 0, 1 => 1],
			'result' => 2,
		];
		yield [
			'items'  => [0 => 1],
			'result' => 2,
		];
		yield [
			'items'  => [],
			'result' => null,
		];
	}
	
	/**
	 * @dataProvider shuffleProvider
	 */
	public function testShuffle($items): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		$collection->shuffle();
		if ($items === []) {
			CollectionTest::assertEquals($items, $collection->all());
		} else {
			CollectionTest::assertNotEquals($items, $collection->all());
		}
	}
	
	public function shuffleProvider(): Generator {
		yield [
			'items' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 'a'],
		];
		yield [
			'items' => [],
		];
	}
	
	/**
	 * @dataProvider reverseProvider
	 */
	public function testReverse($items, $result): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		$collection->reverse();
		CollectionTest::assertEquals($result, $collection->all());
	}
	
	public function reverseProvider(): Generator {
		yield [
			'items'  => [0 => 0, 1 => 1],
			'result' => [0 => 0, 1 => 1],
		];
		yield [
			'items'  => ['k1' => 'y1', 'k2' => 'y2'],
			'result' => ['k2' => 'y2', 'k1' => 'y1'],
		];
		yield [
			'items'  => [],
			'result' => [],
		];
	}
	
	public function testFilter(): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect(45, 44, 2, 4);
		CollectionTest::assertEquals([2, 4], array_values($collection->filter(fn ($item) => $item > 4)->all()));
	}
	
	public function testFilterKeys(): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect(45, 44, 2, 4);
		CollectionTest::assertEquals([2, 4], array_values($collection->filterKeys(0, 1)->all()));
	}
	
	public function testClear(): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect(45, 44, 2, 4);
		$collection->clear();
		CollectionTest::assertEquals([], $collection->all());
	}
	
	public function testResetKeys(): void {
		$collection = new class extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect(['asd' => 1, 'dsa' => 2]);
		$collection->reset();
		CollectionTest::assertEquals([1, 2], $collection->all());
	}
	
	/**
	 * @dataProvider countProvider
	 */
	public function testCount($type, $items, $expectedCount): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->collect($items);
		CollectionTest::assertEquals($expectedCount, $collection->count());
	}
	
	public function countProvider(): Generator {
		yield [
			'type'          => null,
			'items'         => ['item1' => 1, 'item2' => 2, 'item3' => 3],
			'expectedCount' => 3,
		];
		yield [
			'type'          => $this->mockableObject,
			'items'         => ['item1' => 1, 'item2' => 2, 'item3' => $this->getObjectMock()],
			'expectedCount' => 1,
		];
	}
	
	private function getObjectMock(): Route {
		return $this->getMockBuilder($this->mockableObject)->disableOriginalConstructor()->getMock();
	}
}