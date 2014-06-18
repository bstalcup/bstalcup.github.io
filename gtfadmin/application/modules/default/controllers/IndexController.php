<?php

class IndexController extends My_Controller
{
	public $ajaxable = array(
							'index' => array('json')
						);

	public function init()
	{
		if (!$this->view->user->isLoggedIn())
		{
			if (!$this->getRequest()->isXmlHttpRequest())
				$this->_redirect($this->view->links('login'));
			else
				throw new Exception('You are not logged in.', 401);
		}

		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function indexAction()
	{
		$this->view->newFamilies = My_Family::getFamilies(array('parish' => $this->view->user->parish->objectId), 0, 10, array('by' => 'createdAt', 'type' => 'orderByDescending'));

		if ($this->getRequest()->isXmlHttpRequest())
			$this->view->content = $this->view->render('index/index.phtml');
	}
}