<?php

declare(strict_types = 1);

namespace Kikopolis\Controller;

use Kikopolis\Controller\Concerns\RendersViews;

class Controller {
	use RendersViews;
	
	public function home() {
		echo "hi from controller";
	}
}