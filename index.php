<?php

  // Get path and remove base path
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$path = ltrim(str_replace('/citystatus', '', $path), '/');
	$action = explode('/', $path)[0] ?: 'index';

  // user router
  require_once 'controllers/UserController.php';
  $userController = new UserController();
  if ($action === 'signup') {
		$userController->signup();
	} elseif ($action === 'login') {
		$userController->login();
	} else {
		http_response_code(404);
		include 'views/errors/404.php';
	}
?>
