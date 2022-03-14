<?php

declare(strict_types = 1);

namespace Core;

use Generator;
use Kikopolis\Core\Model;
use Kikopolis\Model\Exception\InvalidEmailException;
use Kikopolis\Model\Exception\InvalidLastNameException;
use Kikopolis\Model\Exception\InvalidPasswordException;
use Kikopolis\Model\Exception\InvalidUserNameException;
use Kikopolis\Model\Exception\PasswordMatchException;
use Kikopolis\Model\Exception\TooShortPasswordException;
use Kikopolis\Model\User;
use PHPUnit\Framework\TestCase;
use function dd;
use function in_array;

class ModelTest extends TestCase {
	/**
	 * @dataProvider createProvider
	 */
	public function testUserCreate(array $data, array $errors, bool $result): void {
		$model = User::create($data);
		static::assertEquals($result, $model->validate());
		if (empty($errors) === false) {
			$modelErrors = $model->getErrors();
			foreach ($errors as $error) {
				static::assertTrue(in_array($error, $modelErrors, true));
			}
		}
	}
	
	public function createProvider(): Generator {
		yield 'Normal data, success expected' => [
			'data'   => [
				'firstName'      => 'John',
				'lastName'       => 'Smith',
				'email'          => 'john@example.com',
				'password'       => '123456',
				'passwordRepeat' => '123456',
			],
			'errors' => [],
			'result' => true,
		];
		yield 'Abnormal data, failure expected' => [
			'data'   => [
				'firstName'      => 1,
				'lastName'       => 2,
				'email'          => 3,
				'password'       => '',
				'passwordRepeat' => '332',
			],
			'errors' => [
				InvalidUserNameException::class,
				InvalidLastNameException::class,
				InvalidEmailException::class,
				InvalidPasswordException::class,
				TooShortPasswordException::class,
				PasswordMatchException::class,
			],
			'result' => false,
		];
	}
}