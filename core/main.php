<?php
class Main {

	protected $controller;

    function showPage()
    {
		$pageNumber = 1;

		if (!empty($_GET['page'])) 
		{     
			$pageNumber = (int) $_GET['page'];     
		}
		
		$page_name = '';
		
		switch ($pageNumber) {
			case '1':
				$page_name = 'mainPage';
				break;
			case '2':
				$page_name = 'registrationPage';
				break;
			case '3':
				$page_name = 'loginPage';
				break;
			case '4':
				$page_name = 'logoutPage';
				break;
		}

		if (isset($_POST['submit']) & $pageNumber == 2)
		{
			$this->controller->registration();
			return;
		}
		elseif (isset($_POST['submit']) & $pageNumber == 3)
		{
			if($_POST["token"] == $_SESSION["CSRF"])
			{
				$this->controller->login();
				return;
			}			
		}
		elseif (isset($_GET['code']) & ($pageNumber == 3 || $pageNumber == 2))
		{
			$this->controller->authVK();
			return;			
		}
		elseif ($pageNumber == 4)
		{
			$this->controller->logout();
			return;	
		}

		$this->controller->createPage($page_name.'.php');
		
	}

	function createController()
	{
		$controller_path = "core/controller.php";
		
		if(file_exists($controller_path))
		{
             
			$this->controller = Controller::get();

		}
	}

	

    
}