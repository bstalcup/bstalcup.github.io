<?php

/**
 * My core cronjob plugin.
 *
 * @access public
 */
class My_Plugin_Cron extends Zend_Controller_Plugin_Abstract
{

	private $__options = array();

	/**
	 * @return void
	 */
	public function setOptions($options = null)
	{
		if (!$options)
			return $this;
		foreach ($options as $o)
		{
			$p = explode('=', $o);
			$this->__options[$p[0]] = $p[1];
		}
	}

	/**
	 * @return void
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer');
		$viewRenderer->setNoRender(true);

		$jobName = My_Core::getFront()->getParam('__cron_job');
		$routeConfig = @My_Core::getConfig()->cron->{$jobName};

		if (!$routeConfig)
			throw new My_Exception('Invalid Job specified');

		$request->setModuleName($routeConfig->module)
				->setControllerName($routeConfig->controller)
				->setActionName($routeConfig->action)
				->setParams(array_merge($routeConfig->toArray(), $this->__options));
	}
}