<?php

declare(strict_types = 1);

namespace Core;

use Generator;
use Kikopolis\Core\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase {
	/**
	 * @dataProvider getPathProvider
	 */
	public function testGetPath(string $path, string $expected): void {
		$request = new Request(['REQUEST_URI' => $path, 'REQUEST_METHOD' => 'GET']);
		RequestTest::assertEquals($expected, $request->getPath());
	}
	
	public function getPathProvider(): Generator {
		yield [
			'path'     => '/test',
			'expected' => '/test',
		];
		yield [
			'path'     => '/test?id=1',
			'expected' => '/test',
		];
		yield [
			'path'     => '/test&user=kiko?id=1',
			'expected' => '/test',
		];
		yield [
			'path'     => '',
			'expected' => '/',
		];
	}
	
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
}