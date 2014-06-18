<?php

/**
 * My user abstraction object.
 * @access public
 */
class My_User
{
	public function __construct() 
	{
		
	}	
	
	/* 
	 * login user
	 */
	public function login($username, $password)
	{
		$userSession = new Zend_Session_Namespace('userNamespace');
		$userSession->user = false;

		$parseUser = new My_Parse_User();
		$parseUser->username = $username;
		$parseUser->password = $password;
		$login = $parseUser->login();

		// authentication successfull
		if ($login->request_successfull) 
		{
			$this->objectId = $login->objectId;
			$this->firstName = $login->firstName;
			$this->lastName = $login->lastName;
			$this->username = $login->username;
			$this->email = $login->email;
			$this->isParishAdmin = isset($login->isParishAdmin) ? $login->isParishAdmin : '';
			$this->parish = isset($login->parish) ? $login->parish : '';
			$this->acceptedAgreements = $login->acceptedAgreements;
			$this->sessionToken = $login->sessionToken;
			$this->createdAt = $login->createdAt;
			$this->updatedAt = $login->updatedAt;
			// add it into the session
			$userSession->user = $this;
			
			// check if we need to redirect after login
			$loginRedirect = new Zend_Session_Namespace('loginRedirect');
			if ($loginRedirect->url)
			{
				$view->flash()->redirect($loginRedirect->url);
				unset($loginRedirect->url);
			}

			// great success
			return true;
		}
		
		// couldn't athenticate
		return false;
	}

	public function isLoggedIn()
	{
		$userSession = new Zend_Session_Namespace('userNamespace');
		if (!isset($userSession->user) || !$userSession->user)
			return false;

		return true;
	}

	public function register($email, $password, $firstName, $lastName, $acceptedAgreements)
	{
		$parseUser = new My_Parse_User();
		$register = $parseUser->signup($email, $password, $firstName, $lastName, ($acceptedAgreements == 'on' ? true : false));
		
		// not registered -> throw the error
		if (!$register->request_successfull)
			return $register->error;

		// registered login za user
		$this->login($email, $password);

		return true;
	}

	/* 
	 * fetch user from cache by email
	 */
	public static function getLoggedInUser()
	{
		$userSession = new Zend_Session_Namespace('userNamespace');
		if (isset($userSession->user) && $userSession->user !== false)
			return $userSession->user;

		return false;
	}

	public function logout()
	{
		$userSession = new Zend_Session_Namespace('userNamespace');
		$userSession->user = false;

		return true;
	}

	public static function setPersonToNull($user_id)
	{
		$parseUser = new My_Parse_User();
		$parseUser->person = null;
		$update = $parseUser->update($user_id, null);

		return $update->request_successfull ? true : false;
	}

	public static function assignParish($user_id, $parish_id)
	{
		$parseUser = new My_Parse_User();
		$parseUser->parish = $parseUser->dataType('pointer', array('Parish', $parish_id));
		$update = $parseUser->update($user_id, null);

		return $update->request_successfull ? true : false;
	}

	public static function resetPassword($email)
	{
		$parseUser = new My_Parse_User();
		$reset = $parseUser->requestPasswordReset($email);

		if (isset($reset->error))
			return $reset->error;

		return $reset ? true : false;
	}
}

