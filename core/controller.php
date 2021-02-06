<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\HtmlFormatter;

class Controller {
	public $model;
	public $view;
	protected $log;

	function __construct()
	{
		$this->model = new Model();
		$this->view = new View();
		
		// Создаем логгер 
		$this->log = new Logger('mylogger');

		// Хендлер, который будет писать логи в "mylog.log" и слушать все ошибки с уровнем "WARNING" и выше .
		$this->log->pushHandler(new StreamHandler('mylog.log', Logger::WARNING));

		// Хендлер, который будет писать логи в "troubles.log" и реагировать на ошибки с уровнем "ALERT" и выше.
		$this->log->pushHandler(new StreamHandler('troubles.log', Logger::ALERT));
	}

	public static function get()
	{
	  static $controller = null;
	  if ($controller == null)
		$controller= new Controller();
	  return $controller;
	}

	function createPage(string $viewName)
	{
		$token = null;
		$authorized = $_SESSION["isauth"];		 
		$vk = $_SESSION["isvk"] ;
		
		if ($viewName === 'loginPage.php')
		{
			$token = hash('gost-crypto', random_int(1,999999));
			$_SESSION["CSRF"] = $token;
		}	
		
		$this->view->generate($viewName, $token, $authorized, $vk);
	}

	function registration()
	{
        $err = [];
        // проверяем логин
        if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))
        {
			$err[] = "Логин может состоять только из букв английского алфавита и цифр";
			$this->log->error('Логин может состоять только из букв английского алфавита и цифр');
        } 
        if(strlen($_POST['login']) < 3 || strlen($_POST['login']) > 30)
        {
			$err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
			$this->log->error('Логин должен быть не меньше 3-х символов и не больше 30');
        }
        if (!count($err)>0)
        {
            $userChecked = $this->model->checkUserExistance($_POST['login']);
			
			if (!$userChecked)
            {
				
				$passwordWithSalt = md5($_POST['password'].MEGASECRET);				
				$createUser = $this->model->createUser($_POST['login'], $passwordWithSalt, 'notVK');
                if ($createUser)
                {
                    print "Поздравляем с регистрацией<br>";
                }
                else
                {
					print "Регистрация пошла не так<br>";
					$this->log->error('Регистрация пошла не так');
                } 
                header("Location: /index.php?page=1");
			}
 			else
        	{
				print "<b>Пользователь с таким логином уже существует</b><br>";
				$this->log->error('Пользователь с таким логином уже существует');      
        	} 

		}      
	}
	
	function login()
	{
		$passwordWithSalt = md5($_POST['password'].MEGASECRET);
		$login = $this->model->getUser($_POST['login'], $passwordWithSalt);
		
		if($login)
        {    
			$_SESSION["isauth"] = true;
			header("Location: /index.php?page=1");
        }
        else
        {
			print "<b>Вы ввели неверные данные</b><br>";
			$this->log->error('Вы ввели неверные данные');      
        }
	}
	
	function authVK()
	{
		$params;

		if (isset($_GET['code'])) {
				$params = array(
					'client_id' => id,
					'client_secret' => key,
					'code' => $_GET['code'],
					'redirect_uri' => uri
				);
		}
		$token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);
			
		if (isset($token['access_token'])) {
        $params = array(
			'uids' => $token['user_id'],
            'fields' => 'uid,first_name,last_name,screen_name,sex,bdate,photo_big',
            'access_token' => $token['access_token']
        );
		}
		
		$userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))."&v=5.21"), true);

		if (isset($userInfo['response'][0]['id'])) {
			$userInfo = $userInfo['response'][0];
			$result = true;
		}

		$userChecked = $this->model->checkUserExistance($userInfo['id']);

		if (!$userChecked)
		{
			$createUser = $this->model->createUser($userInfo['id'], '', 'vk');
		}

		$_SESSION["isvk"] = true;
		$_SESSION["isauth"] = true;
		header("Location: /index.php?page=1");
	}

	function logout()
	{
		$_SESSION["isauth"] = false;
		$_SESSION["isvk"] = false;
		header("Location: /index.php?page=1");
	}
}