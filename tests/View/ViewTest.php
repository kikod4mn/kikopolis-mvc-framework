<?php

declare(strict_types = 1);

namespace Kikopolis\tests\View;

use Generator;
use Kikopolis\View\TemplateFileLoader;
use Kikopolis\View\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {
	public function testRenderWithParentTemplate(): void {
		$templateLoader = $this->getMockBuilder(TemplateFileLoader::class)->getMock();
		$templateLoader->expects($this->exactly(2))
					   ->method('get')
					   ->withConsecutive(['home'], ['layouts.main'])
					   ->willReturnOnConsecutiveCalls("@merge(layouts.main)\n\r<h1>Hi!</h1>", '<title>A TITLE</title>{{ @content }}')
		;;
		$view  = new View($templateLoader);
		$ready = $view->render('home');
		ViewTest::assertFalse(str_contains($ready, '@merge'));
		ViewTest::assertFalse(str_contains($ready, '{{ @content }}'));
		ViewTest::assertTrue(str_contains($ready, '<title>A TITLE</title>'));
		ViewTest::assertTrue(str_contains($ready, '<h1>Hi!</h1>'));
	}
}