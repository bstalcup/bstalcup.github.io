<?php

class User_IndexController extends My_Controller
{
	public $ajaxable = array(
							'register' => array('json'),
							'login' => array('json'), 
							'logout' => array('json'),
							'forgotpassword' => array('json'),
							'findparish' => array('json')
						);

	public function init()
	{
		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function loginAction()
	{
		$request = $this->getRequest();
		
		if ($this->view->user->isLoggedIn())
		{
			if ($this->view->flashRedirect) $this->_redirect($this->view->flashRedirect);
				else $this->_redirect($this->view->links('index'));
		}

		if ($request->isPost()) 
		{ 
			if (trim($request->getPost('email')) && trim($request->getPost('password')))
			{ 
				if (!Zend_Validate::is($request->getPost('email'), 'EmailAddress'))
					return $this->view->bridge()->warning($this->view->translate('Invalid email address. Please try again or contact us.'));

				$loggedIn = $this->view->user->login(strtolower(trim($request->getPost('email'))), trim($request->getPost('password')));
				if ($loggedIn) { 
					if (!in_array($this->view->routeOptions['route_name'], array('login', 'logout'))) $this->view->bridge()->refresh();
						else $this->view->bridge()->redirect($this->view->links('index'));
				} else {
					$this->view->bridge()->addCallback('$("#showLoginError").show()');
				}
			} else {
				$this->view->bridge()->addCallback('$("#showLoginError").show()');
			}
		}
		else
			$this->_helper->layout->setLayout('layout_login');
	}

	public function logoutAction()
	{
		$this->view->user->logout();

		if (!$this->getRequest()->isXmlHttpRequest())
		{ 
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_redirect($this->view->links('index'));
		} else {
			$this->view->bridge()->redirect($this->view->links('index'));
		}
	}
	
	public function findparishAction()
	{
		$request = $this->getRequest();

		// if the user does not come from the register page -> log him out!
		$userSession = new Zend_Session_Namespace('userNamespace');
		if (!isset($userSession->user) || !isset($userSession->user->justRegistered))
			$this->_redirect($this->view->links('logout'));
		
		if ($request->isPost() && $request->getParam('church_type_name'))
		{
			// add the parish if it does not exist + assign it to the user
			$checkFields = array(
				'name' => 'name', 
				'addressLine1' => 'church_address_street_address',
				'addressCity' => 'church_address_city_name',
				'addressState' => 'church_address_providence_name',
				'addressZip' => 'church_address_postal_code'
			);
			foreach ($checkFields as $key => $value) {
				if ($request->getParam($value) && $request->getParam($value) != '')
					$filters[$key] = $request->getParam($value);
			}
			$parish = My_Parish::getParishes($filters);
			// if the parish does not exist -> add the parish
			if ($parish !== false && count($parish) == 0)
			{
				$values = array(
					'name' => $request->getParam('name'),
					'addressCity' => $request->getParam('church_address_city_name'), 
					'addressState' => $request->getParam('church_address_providence_name'), 
					'addressZip' => $request->getParam('church_address_postal_code'), 
					'phone' => $request->getParam('phone_number'), 
					'addressLine1' => $request->getParam('church_address_street_address'), 
					'parishCoordinates' => array($request->getParam('latitude'), $request->getParam('longitude')),
					'website' => $request->getParam('url'),
					'isOneParishEnabled' => false
				);
				$parish = My_Parish::addParish($values);
			}
			else $parish = $parish[0];

			if (My_User::assignParish($this->view->user->objectId, $parish->objectId))
			{	
        		$parishObject = new stdClass();
				$parishObject->__type = 'Pointer';
				$parishObject->className = 'Parish';
				$parishObject->objectId = $parish->objectId;
				$userSession = new Zend_Session_Namespace('userNamespace');
				$userSession->user->parish = $this->view->user->parish = $parishObject;

				unset($userSession->user->justRegistered);

				return $this->view->bridge()->redirect($this->view->links('index'));
			}

			return $this->view->bridge()->addCallback('showResetMsg("alert-danger", "Something went wrong. Please refresh the page and try again.")');

		}

		//nothing
		$this->_helper->layout->setLayout('layout_login');
	}

	public function forgotpasswordAction()
	{
		$request = $this->getRequest();
		
		if ($request->isPost() && $request->getParam('email'))
		{
			$response = My_User::resetPassword($request->getParam('email'));
			if ($response === true)
				return $this->view->bridge()->addCallback('showResetMsg("alert-success", "We received your request. Check your email for reset instructions.")');
			elseif (is_string($response))
				return $this->view->bridge()->addCallback('showResetMsg("alert-danger", "'. ucfirst($response) .'")');
			else
				return $this->view->bridge()->addCallback('showResetMsg("alert-danger", "Something went wrong. Please refresh the page and try again.")');
		}

		$this->_helper->layout->setLayout('layout_login');
	}

	public function registerAction()
	{
		$request = $this->getRequest();
		
		if ($request->isPost())
		{
			if (trim($request->getPost('email')) && trim($request->getPost('password')) && trim($request->getPost('firstName')) && trim($request->getPost('lastName')) && $request->getPost('acceptedAgreements'))
			{ 
				if (!Zend_Validate::is($request->getPost('email'), 'EmailAddress'))
					return $this->view->bridge()->addCallback('showResetMsg("alert-danger", "Invalid email address. Please try again or contact us.")');

				$user = new My_User();
				$response = $user->register($request->getPost('email'), $request->getPost('password'), $request->getPost('firstName'), $request->getPost('lastName'), $request->getPost('acceptedAgreements'));
				if ($response !== true)
					return $this->view->bridge()->addCallback('showResetMsg("alert-danger", "'. ucfirst($response) .'")');

				// this is just to give the user access to find-parish
				$userSession = new Zend_Session_Namespace('userNamespace');
				if (isset($userSession->user))
					$userSession->user->justRegistered = true;	

				return $this->view->bridge()->redirect($this->view->links('find_parish'));
			}
			return $this->view->bridge()->addCallback('showResetMsg("alert-danger", "Something went wrong. Please refresh the page and try again.")');
		}

		$this->_helper->layout->setLayout('layout_login');
	}

	public function noparishAction()
	{
		$this->_helper->layout->setLayout('layout_login');
	}
}
?>