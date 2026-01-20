<?php

  // if Api request, route to ApiController
  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $path = trim($path, '/'); 
  $segments = explode('/', $path);

  // Check if it starts with api/user
  if (isset($segments[1]) && $segments[1] === 'api' && isset($segments[2]) && $segments[2] === 'user') {
    require_once 'api/UserAPI.php';
    $controller = new UserApiController();
    
    // The action is the 3rd part of the URL (e.g., api/user/login)
    $action = $segments[3] ?? '';
    error_log("API Action detected: " . $action);
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($action) {
        case 'login':
            if ($method === 'POST') $controller->login();
            else http_response_code(405);
            break;

        case 'signup':
            if ($method === 'POST') $controller->signup();
            else http_response_code(405);
            break;

        case 'health-check':
            if ($method === 'GET') $controller->healthCheck();
            else http_response_code(405);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
    exit;
  }

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
