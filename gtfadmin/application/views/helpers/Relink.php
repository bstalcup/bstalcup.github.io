<?php

class My_Helper_Relink extends Zend_View_Helper_Abstract
{
	public $view = null;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
		if (!$this->view->routeOptions)
			$this->view->routeOptions = array();
	}

	public function relink($params = array())
	{
		$params = array_merge($_GET, $params);
		return $this->view->url() . '?' . http_build_query($params);
	}
}