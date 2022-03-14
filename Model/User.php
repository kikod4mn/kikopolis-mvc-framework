<?php

declare(strict_types = 1);

namespace Kikopolis\Model;

use Kikopolis\Core\Model;

final class User extends Model {
	private string $firstName;
	private string $lastName;
	private string $email;
	private string $password;
	private string $passwordRepeat;
	
	public function __construct(string $firstName = '', string $lastName = '', string $email = '', string $password = '', string $passwordRepeat = '') {
		$this->firstName      = $firstName;
		$this->lastName       = $lastName;
		$this->email          = $email;
		$this->password       = $password;
		$this->passwordRepeat = $passwordRepeat;
	}
}