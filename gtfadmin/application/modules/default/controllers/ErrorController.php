<?php

class ErrorController extends My_Controller
{
	public $ajaxable = array(
							'fallback' => array('json')
						);

	public function init()
	{
		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function errorAction()
	{
		// get trace
		$trace = $this->_getParam('error_handler')->exception->getTrace();

		// only trace last 6 functions
		if (count($trace) > 6)
			$trace = array_slice($trace, 0, 6);

		// default message to exception title
		$this->view->message = $this->_getParam('error_handler')->exception->getMessage();

		switch (My_Core::getConfig()->debug->level){
			case 1 : 
				// notify user of error
				$this->view->bridge()->emerg($this->view->message);
				// log trace to file
				My_Core::getLogger()->debug('---DEBUG START===');
				My_Core::getLogger()->debug($trace);
				My_Core::getLogger()->debug('===DEBUG START---');
				break;
			case 2 : 
				// notify user of error
				$this->view->bridge()->emerg($this->view->message);
				// show user error details including trace
				$this->view->bridge()->debug($trace);
				break;
			default : 
				// log original message
				My_Core::getLogger()->emerg($this->view->message);
				// do not show user error details
				$this->view->message = $this->view->translate('We\'re sorry, an error has occured. Please contact us if this problem persists.');
				// notify user of error
				$this->view->bridge()->emerg($this->view->message);
				// log trace to file
				$trace = "\n\r ---DEBUG START===\n\r" . Zend_Debug::dump($trace, null, false) . "\n\r ===DEBUG END---\n\r";
				My_Core::getLogger()->debug(html_entity_decode($trace));
				break;
		}

		if ($this->getRequest()->isXmlHttpRequest()){
			$this->_helper->viewRenderer->setNoRender(true);
			if ($this->view->message == 'You are not logged in.')
				die(json_encode(array("redirect" => "/login")));
		}
	}

	public function fallbackAction() 
	{
		if ($this->getRequest()->isXmlHttpRequest()){
			return $this->view->bridge()->crit($this->view->translate('Page not found'));
		}
	}

}