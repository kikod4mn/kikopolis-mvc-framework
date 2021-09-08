<?php

declare(strict_types = 1);

namespace Kikopolis\Controller;

use Kikopolis\Controller\Concerns\RendersViews;
use Kikopolis\Core\Request;
use Kikopolis\Core\Response;

class Controller {
	use RendersViews;
	
	public function home(): Response {
		return $this->render('home', ['name' => 'Kristo Leas']);
	}
	
	public function contact(): Response {
		return $this->render('contact');
	}
	
	public function handleContact(Request $request): Response {
		dd($request->getBody());
	}
	
	public function pricing(): Response {
		return $this->render('pricing');
	}
}