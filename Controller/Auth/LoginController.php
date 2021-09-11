<?php

declare(strict_types = 1);

namespace App\Controller\Auth;

use App\Controller\Concerns\RendersViews;
use Kikopolis\Core\Response;

final class LoginController {
	use RendersViews;
	
	public function login(): Response {
		return $this->render('auth/login');
	}
	
	public function handleLogin() {
		// todo
	}
	
	public function logout() {
		// todo
	}
}