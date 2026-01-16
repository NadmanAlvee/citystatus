<?php
class UserController
{
  public function login()
	{
		$this->loadView('authentications/login');
	}
  public function signup()
	{
		$this->loadView('authentications/signup');
	}
  private function loadView($view, $data = [])
	{
		extract($data);

		$viewFile = "views/{$view}.php";
		if (file_exists($viewFile)) {
			include $viewFile;
		} else {
			http_response_code(404);
			echo "View not found: {$view}";
		}
	}
}
?>