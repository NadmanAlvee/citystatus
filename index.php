<?php

  // if Api request, route to ApiController
  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $path = trim($path, '/'); 
  $segments = explode('/', $path);

  if (isset($segments[1]) && $segments[1] === 'api') {
      $resource = $segments[2] ?? '';
      $action   = $segments[3] ?? '';
      $method   = $_SERVER['REQUEST_METHOD'];

      // --- USER API ---
      if ($resource === 'user') {
          require_once 'api/UserAPI.php';
          $controller = new UserApiController();

          switch ($action) {
              case 'login':
                  if ($method === 'POST') $controller->login();
                  else http_response_code(405);
                  break;
              case 'signup':
                  if ($method === 'POST') $controller->signup();
                  else http_response_code(405);
                  break;
              case 'logout':
                  if ($method === 'POST') $controller->logout();
                  else http_response_code(405);
                  break;
              case 'forgotPassword':
                  if ($method === 'POST') $controller->forgotPassword();
                  else http_response_code(405);
                  break;
              case 'resetPassword':
                  if ($method === 'POST') $controller->resetPassword();
                  else http_response_code(405);
                  break;
              case 'update':
                  if ($method === 'POST') $controller->update();
                  break;
              case 'getUsers':
                  if ($method === 'GET') $controller->getUsers();
                  break;
              case 'deleteUser':
                  if ($method === 'POST') $controller->deleteUser();
                  break;
              default:
                  http_response_code(404);
                  echo json_encode(['error' => 'User endpoint not found']);
          }
          exit;
      }

      // --- POST/AREA API ---
      if ($resource === 'post') {
          require_once 'api/PostAPI.php';
          $controller = new PostApiController();

          switch ($action) {
              case 'getPosts':
                  $controller->getPosts();
                  break;
              case 'UpvoteOrDownvote':
                  $controller->UpvoteOrDownvote();
                  break;
              case 'addPost':
                  $controller->addPost();
                  break;
              case 'deletePost':
                  if ($method === 'POST') $controller->deletePost();
                  break;
              case 'getAreas':
                  $controller->getAreas();
                  break;
              case 'addArea':
                  if ($method === 'POST') $controller->addArea();
                  break;
              default:
                  http_response_code(404);
                  echo json_encode(['error' => 'Post endpoint not found']);
          }
          exit;
      }
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
