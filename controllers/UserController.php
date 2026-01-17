<?php
class UserController
{
  public function home()
  {
    $this->loadView('Homepage');
  }
  public function login()
	{
		$this->loadView('authentications/Login');
	}
  public function signup()
	{
		$this->loadView('authentications/Signup');
	}


	public function forgotpassword()
	{
		$this->loadView('authentications/ForgotPassword');
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