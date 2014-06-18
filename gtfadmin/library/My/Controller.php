<?php

class My_Controller extends Zend_Controller_Action 
{
	protected $_test = false;

	public function init()
	{
		$userSession = new Zend_Session_Namespace('userNamespace');
	
		if (My_Core::getFront()->getParam('__cron'))
			return;

		if (isset(My_Core::getConfig()->application->test) && My_Core::getConfig()->application->test == true)
			$this->_test = true;

		if ($this->view->user->isLoggedIn() && !$this->getRequest()->isXmlHttpRequest())
		{
			// extract the required families
			if (isset($this->view->user->parish->objectId))
			{
				$filters = array();
				$filters['parish'] = $this->view->user->parish->objectId;
				$filters['approved'] = true;
				$familiesCount = My_Family::getFamiliesCount($filters);
				$this->view->approvedFamilies = $familiesCount;
			}
		}

		$request = $this->getRequest();
		$this->view->routeName =  $request->getParam('route_name');
		$this->view->routeOptions = $request->getParams();
		if (!isset($this->view->routeOptions['menu']))
			$this->view->routeOptions['menu'] = 'index';
		$this->view->routeMenu = $this->view->routeOptions['menu'];
		if (!isset($this->view->routeOptions['submenu']))
			$this->view->routeOptions['submenu'] = 'index';
		$this->view->routeSubmenu = $this->view->routeOptions['submenu'];
	}

	public function fallbackAction()
	{
		return $this->_forward('fallback', 'error', 'default');
	}
}