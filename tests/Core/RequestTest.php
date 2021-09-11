<?php

declare(strict_types = 1);

namespace Core;

use Generator;
use Kikopolis\Core\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase {
	/**
	 * @dataProvider getMethodProvider
	 */
	public function testGetMethod(string $method, string $expected): void {
		$request = new Request(['REQUEST_METHOD' => $method]);
		RequestTest::assertEquals($expected, $request->getMethod());
	}
	
	public function getMethodProvider(): Generator {
		yield [
			'method'   => 'get',
			'expected' => 'GET',
		];
		yield [
			'method'   => 'post',
			'expected' => 'POST',
		];
	}
	
	/**
	 * @dataProvider isGetProvider
	 */
	public function testIsGet(string $method, bool $expected): void {
		$request = new Request(['REQUEST_METHOD' => $method]);
		RequestTest::assertEquals($expected, $request->isGet());
	}
	
	public function isGetProvider(): Generator {
		yield [
			'method'   => 'get',
			'expected' => true,
		];
		yield [
			'method'   => 'post',
			'expected' => false,
		];
	}
	
	/**
	 * @dataProvider isPostProvider
	 */
	public function testIsPost(string $method, bool $expected): void {
		$request = new Request(['REQUEST_METHOD' => $method]);
		RequestTest::assertEquals($expected, $request->isPost());
	}
	
	public function isPostProvider(): Generator {
		yield [
			'method'   => 'get',
			'expected' => false,
		];
		yield [
			'method'   => 'post',
			'expected' => true,
		];
	}
}