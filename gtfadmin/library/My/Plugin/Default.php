<?php

/**
 * My core plugin.
 * 
 * @access public
 */
class My_Plugin_Default extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Called before dispatch. Sets up view, adds helper & filter paths
	 *
	 * @return void
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		$view = new Zend_View();
		$view->addHelperPath(My_Core::getLayoutHelperPath(), 'My_Helper')
			->addFilterPath(My_Core::getLayoutFilterPath(), 'My_Filter');

		$viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer');
		$viewRenderer->setView($view);

		$view->headTitle()->setSeparator(' | ');

		$view->user = My_Core::getUser();
	}
}