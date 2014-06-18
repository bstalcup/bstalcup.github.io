<?php

class Cron_ExController extends My_Controller
{
	/*
	 * Main cleanup job 
	 */
	public function indexAction() 
	{
		$request = $this->getRequest();
		echo date('[Y-m-d H:i:s] - ') . "[Start] \"Ex job\".\n";
		ob_flush();

		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Do your thing here!
	}
}
