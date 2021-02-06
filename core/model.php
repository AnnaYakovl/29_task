<?php
class Model
{
   //Проверям, существует ли пользователь
   function checkUserExistance(string $userLogin)
   {
	   $userExist = true;
	   
	   $dateBase = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	   $sql = "select id from users where user_login = '$userLogin'"; 

	   $result = $dateBase->query($sql);

	   if($result === false)
	   {
		   $userExist = false;
	   }  
	   
	   return $userExist;
   }

   //Создаём пользователя
   function createUser(string $userLogin, string $userPassword, string $vk)
   {
	   $sql = "insert into users(user_login, user_password, user_role) 
	   values ('".$userLogin."', '".$userPassword."', '".$vk."');"; 
	   $dateBase = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	   $result =  $dateBase ->exec($sql); 
	   if($result)
	   {
		   return true;
	   }  
	   return false; 
   }


   function getUser(string $userLogin, string $password)
   {
	   $userFind = false;
	   $dateBase = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	
	   $sql = "select id, user_password, user_role from users where user_login = '$userLogin' LIMIT 1";
	   $result = $dateBase->query($sql);
	   $resultArray = $result->FETCH(PDO::FETCH_ASSOC);

	   $userRole = $resultArray['user_role'];
	   $userPassword = $resultArray['user_password'];
	   
	   if ($userRole === 'vk')
	   {
		   $userFind = true;
	   }
	   elseif($userPassword  === $password)
	   {
		   $userFind =  true;
	   }

	   return $userFind;           
   }
}