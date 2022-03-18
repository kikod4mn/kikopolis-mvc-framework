<?php

declare(strict_types = 1);

router()->get('/', [App\Controller\HomeController::class, 'home']);
router()->get('/games', [App\Controller\HomeController::class, 'games']);
//router()->get('/contact', [App\Controller\Controller::class, 'contact']);
//router()->post('/contact', [App\Controller\Controller::class, 'handleContact']);
//router()->get('/pricing', [App\Controller\Controller::class, 'pricing']);

//router()->get('/register', [App\Controller\Auth\RegisterController::class, 'register']);
//router()->post('/register', [App\Controller\Auth\RegisterController::class, 'store']);
//router()->get('/login', [App\Controller\Auth\LoginController::class, 'login']);
//router()->post('/handle-login', [App\Controller\Auth\LoginController::class, 'handleLogin']);
//router()->get('/logout', [App\Controller\Auth\LoginController::class, 'logout']);
