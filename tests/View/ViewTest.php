<?php

declare(strict_types = 1);

namespace Kikopolis\tests\View;

use Generator;
use Kikopolis\View\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {
	/**
	 * @dataProvider renderProvider
	 */
	public function testRender(string $content): void {
		$view  = new View();
		$ready = $view->render($content);
		ViewTest::assertFalse(str_contains($ready, '@merge'));
	}
	
	public function renderProvider(): Generator {
		yield [
			'content' => "@merge(layouts.main)\n\r<h1>Hi!<h1>",
		];
	}
}