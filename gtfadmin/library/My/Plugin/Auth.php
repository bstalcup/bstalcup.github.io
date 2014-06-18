<?php

class My_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Called before dispatch. checks permissions and requires login if needed.
	 *
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if (My_Core::getFront()->getParam('__cron'))
			return;

		$user = My_User::getLoggedInUser();
		if (!$user)
		{
			if (!in_array($request->getParam('route_name'), array('login', 'forgot_password', 'register')))
			{
				if ($request->isXmlHttpRequest())
					if (!($request->isPost() && $request->getPost('action') == 'login'))
						die(json_encode(array("redirect" => '/login')));

				return $request->setModuleName('user')
					->setControllerName('index')
					->setActionName('login')
					->setParams(array());
			}
		}

		if ($user && empty($user->parish) && $request->getParam('route_name') != 'logout' && $request->getParam('route_name') != 'find_parish')
		{
			if ($request->isXmlHttpRequest())
				die(json_encode(array("redirect" => '/no-parish')));

			return $request->setModuleName('user')
				->setControllerName('index')
				->setActionName('noparish')
				->setParams(array());
		}
	}
}