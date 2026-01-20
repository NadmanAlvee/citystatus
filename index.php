<?php

  // Get path and remove base path
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$path = ltrim(str_replace('/citystatus', '', $path), '/');
	$action = explode('/', $path)[0] ?: 'index';

  // user router
  require_once 'controllers/UserController.php';
  $userController = new UserController();
  if ($action === 'index') {
    $userController->home();
  } elseif ($action === 'signup') {
		$userController->signup();
	} elseif ($action === 'login') {
		$userController->login();
	} elseif ($action === 'forgotpassword') {
		$userController->forgotpassword();
	} elseif ($action === 'admin-dashboard') {
		$userController->adminDashboard();
	} elseif ($action === 'user-dashboard') {
		$userController->userDashboard();
	} else {
		http_response_code(404);
		include 'views/errors/NotFoundPage.php';
	}
?>
