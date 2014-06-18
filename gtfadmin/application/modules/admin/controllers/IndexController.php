<?php

class Admin_IndexController extends My_Controller
{
	public $ajaxable = array(
						);

	public function init()
	{
		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function indexAction()
	{

	}
}