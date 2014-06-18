<?php

class My_Helper_Links extends Zend_View_Helper_Abstract
{
	public $view = null;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
		if (!$this->view->routeOptions)
			$this->view->routeOptions = array();
	}

	public function links($route, $params = array())
	{
		return $this->view->url($params, $route);
	}
}