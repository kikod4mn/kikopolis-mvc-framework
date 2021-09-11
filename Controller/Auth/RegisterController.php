<?php

declare(strict_types = 1);

namespace App\Controller\Auth;

use App\Controller\Concerns\RendersViews;
use Kikopolis\Core\Response;

final class RegisterController {
	use RendersViews;
	
	public function register(): Response {
		return $this->render('auth/register');
	}
	
	public function store() {
	
	}
}