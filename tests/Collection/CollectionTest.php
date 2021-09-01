<?php

declare(strict_types = 1);

namespace tests\Collection;

use Generator;
use Kikopolis\Core\Collection\Collection;
use Kikopolis\Core\Router\Route;
use PHPUnit\Framework\TestCase;
use function each;

/**
 * Class CollectionTest
 * @package tests\Collection
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
final class CollectionTest extends TestCase {
	/**
	 * Define a random class to use as a test for the collection type system.
	 * Any class will do as constructor is disabled.
	 * @var string
	 */
	private string $mockableObject = Route::class;
	
	/**
	 * @dataProvider allProvider
	 */
	public function testAll($type, $items, $all): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		foreach ($items as $key => $value) {
			$collection->add($key, $value);
		}
		CollectionTest::assertEquals($all, $collection->all());
	}
	
	public function allProvider(): Generator {
		yield [
			'type'  => null,
			'items' => ['item1' => 1, 'item2' => 2, 'item3' => 3],
			'all'   => ['item1' => 1, 'item2' => 2, 'item3' => 3],
		];
		yield [
			'type'  => $this->mockableObject,
			'items' => ['item1' => 1, 'item2' => 2, 'item3' => $this->getObjectMock()],
			'all'   => ['item3' => $this->getObjectMock()],
		];
	}
	
	/**
	 * @dataProvider existsProvider
	 */
	public function testExists($type, $key, $value): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->add($key, $value);
		if ($type !== null) {
			CollectionTest::assertTrue($collection->get($key) instanceof $type);
		}
		CollectionTest::assertTrue($collection->exists($key));
	}
	
	public function existsProvider(): Generator {
		yield [
			'type'  => null,
			'key'   => 0,
			'value' => 'string',
		];
		yield [
			'type'  => null,
			'key'   => 'key',
			'value' => 'value',
		];
		yield [
			'type'  => $this->mockableObject,
			'key'   => 0,
			'value' => $this->getObjectMock(),
		];
	}
	
	/**
	 * @dataProvider containsProvider
	 */
	public function testContains($type, $key, $value): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->add($key, $value);
		if ($type !== null) {
			CollectionTest::assertTrue($collection->get($key) instanceof $type);
		}
		CollectionTest::assertTrue($collection->contains($value));
	}
	
	public function containsProvider(): Generator {
		yield [
			'type'  => null,
			'key'   => 0,
			'value' => 'string',
		];
		yield [
			'type'  => null,
			'key'   => 'key',
			'value' => 'value',
		];
		yield [
			'type'  => $this->mockableObject,
			'key'   => 0,
			'value' => $this->getObjectMock(),
		];
	}
	
	/**
	 * @dataProvider emptyProvider
	 */
	public function testEmpty($type, $items, $result): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		if ($items !== []) {
			foreach ($items as $key => $value) {
				$collection->add($key, $value);
			}
		}
		CollectionTest::assertEquals($result, $collection->empty());
	}
	
	public function emptyProvider(): Generator {
		yield [
			'type'   => $this->mockableObject,
			'items'  => [
				'key1'   => 0,
				'value1' => $this->getObjectMock(),
				'key2'   => 1,
				'value2' => $this->getObjectMock(),
			],
			'result' => false,
		];
		yield [
			'type'   => $this->mockableObject,
			'items'  => [],
			'result' => true,
		];
	}
	
	/**
	 * @dataProvider getProvider
	 */
	public function testGet($type, $key, $value): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->add($key, $value);
		if ($type !== null) {
			CollectionTest::assertTrue($collection->get($key) instanceof $type);
		}
		CollectionTest::assertEquals($value, $collection->get($key));
	}
	
	public function getProvider(): Generator {
		yield [
			'type'  => null,
			'key'   => 0,
			'value' => 'string',
		];
		yield [
			'type'  => null,
			'key'   => 'key',
			'value' => 'value',
		];
		yield [
			'type'  => $this->mockableObject,
			'key'   => 0,
			'value' => $this->getObjectMock(),
		];
	}
	
	/**
	 * @dataProvider keyProvider
	 */
	public function testKey($type, $key, $value): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->add($key, $value);
		if ($type !== null) {
			CollectionTest::assertTrue($collection->get($key) instanceof $type);
		}
		CollectionTest::assertEquals($key, $collection->key($value));
	}
	
	public function keyProvider(): Generator {
		yield [
			'type'  => null,
			'key'   => 0,
			'value' => 'string',
		];
		yield [
			'type'  => null,
			'key'   => 'key',
			'value' => 'value',
		];
		yield [
			'type'  => $this->mockableObject,
			'key'   => 0,
			'value' => $this->getObjectMock(),
		];
	}
	
	/**
	 * @dataProvider addProvider
	 */
	public function testAdd($type, $key, $value): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->add($key, $value);
		if ($type !== null) {
			CollectionTest::assertTrue($collection->get($key) instanceof $type);
		}
		CollectionTest::assertEquals($value, $collection->get($key));
	}
	
	public function addProvider(): Generator {
		yield [
			'type'  => null,
			'key'   => 0,
			'value' => 'string',
		];
		yield [
			'type'  => null,
			'key'   => 'key',
			'value' => 'value',
		];
		yield [
			'type'  => $this->mockableObject,
			'key'   => 0,
			'value' => $this->getObjectMock(),
		];
	}
	
	/**
	 * @dataProvider removeProvider
	 */
	public function testRemove($type, $key1, $value1, $key2, $value2): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		$collection->add($key1, $value1);
		$collection->add($key2, $value2);
		if ($type !== null) {
			CollectionTest::assertTrue($collection->get($key1) instanceof $type);
		}
		CollectionTest::assertEquals($value1, $collection->get($key1));
		if ($type !== null) {
			CollectionTest::assertTrue($collection->get($key2) instanceof $type);
		}
		CollectionTest::assertEquals($value2, $collection->get($key2));
		$collection->remove($key2);
		CollectionTest::assertEquals($value1, $collection->get($key1));
		CollectionTest::assertEquals(null, $collection->get($key2));
	}
	
	public function removeProvider(): Generator {
		yield [
			'type'   => null,
			'key1'   => 0,
			'value1' => 'string',
			'key2'   => 1,
			'value2' => 'integer',
		];
		yield [
			'type'   => null,
			'key1'   => 'key1',
			'value1' => 'value1',
			'key2'   => 'key2',
			'value2' => 'boolean',
		];
		yield [
			'type'   => $this->mockableObject,
			'key1'   => 0,
			'value1' => $this->getObjectMock(),
			'key2'   => 1,
			'value2' => $this->getObjectMock(),
		];
	}
	
	/**
	 * @dataProvider eachProvider
	 */
	public function testEach($type, $items, $result): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		foreach ($items as $key => $value) {
			$collection->add($key, $value);
		}
		CollectionTest::assertEquals($result, $collection->each(fn ($item) => $item === $result));
	}
	
	public function eachProvider(): Generator {
		yield [
			'type'   => null,
			'items'  => [0 => true, 1 => true, 2 => false],
			'result' => false,
		];
		yield [
			'type'   => null,
			'items'  => [0 => true, 1 => true, 2 => true],
			'result' => true,
		];
	}
	
	/**
	 * @dataProvider countProvider
	 */
	public function testCount($type, $items, $expectedCount): void {
		$collection = new class($type) extends Collection {
			public function __construct(string $type = null) { parent::__construct($type); }
		};
		foreach ($items as $key => $value) {
			$collection->add($key, $value);
		}
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