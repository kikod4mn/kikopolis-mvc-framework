<?php

declare(strict_types = 1);

namespace Kikopolis\tests\Support;

use Generator;
use Kikopolis\Support\Arr;
use PHPUnit\Framework\TestCase;

final class ArrTest extends TestCase {
	/**
	 * @dataProvider associativeProvider
	 */
	public function testAssociative(array $array, bool $expected): void {
		ArrTest::assertEquals($expected, Arr::associative($array));
	}
	
	public function associativeProvider(): Generator {
		yield [
			'array'    => [1, 2, 3],
			'expected' => true,
		];
		yield [
			'array'    => [1 => 1, 2 => 2, 0],
			'expected' => true,
		];
		yield [
			'array'    => ['asd' => 1, 0 => 2],
			'expected' => false,
		];
	}
}