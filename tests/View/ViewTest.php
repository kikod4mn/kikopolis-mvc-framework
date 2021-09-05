<?php

declare(strict_types = 1);

namespace Kikopolis\tests\View;

use Kikopolis\View\TemplateFileLoader;
use Kikopolis\View\View;
use PHPUnit\Framework\TestCase;
use function str_contains;

class ViewTest extends TestCase {
	public function testRenderWithParentTemplate(): void {
		$templateLoader = $this->getMockBuilder(TemplateFileLoader::class)->getMock();
		$templateLoader->expects(static::exactly(2))
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
	
	public function testRenderWithoutParentTemplate(): void {
		$templateLoader = $this->getMockBuilder(TemplateFileLoader::class)->getMock();
		$templateLoader->expects(static::once())
					   ->method('get')
					   ->with('home')
					   ->willReturn("<h1>Hi!</h1>")
		;;
		$view  = new View($templateLoader);
		$ready = $view->render('home');
		ViewTest::assertTrue(str_contains($ready, '<h1>Hi!</h1>'));
	}
	
	public function testTitleParsing(): void {
		$templateLoader = $this->getMockBuilder(TemplateFileLoader::class)->getMock();
		$templateLoader->expects(static::once())
					   ->method('get')
					   ->with('home')
					   ->willReturn("{{ @title(Contact us) }}<h1>Hi!</h1>")
		;;
		$view  = new View($templateLoader);
		$ready = $view->render('home');
		ViewTest::assertTrue(str_contains($ready, '<title>Contact us</title>'));
		ViewTest::assertTrue(str_contains($ready, '<h1>Hi!</h1>'));
	}
}