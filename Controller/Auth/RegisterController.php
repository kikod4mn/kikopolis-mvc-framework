<?php

declare(strict_types = 1);

namespace App\Controller\Auth;

use App\Controller\Concerns\RendersViews;
use Kikopolis\Core\Request;
use Kikopolis\Core\Response;
use Kikopolis\Model\User;
use function dd;

final class RegisterController {
	use RendersViews;
	
	public function register(): Response {
		return $this->render('auth/register');
	}
	
	public function store(Request $request): Response {
		dd(User::create($request->getBody())->validate());
	}
}