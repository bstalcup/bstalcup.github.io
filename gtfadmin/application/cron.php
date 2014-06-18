<?php

// setup root path
$rootPath = realpath(dirname(dirname(__FILE__)));
chdir($rootPath . '/public');

// setup root path
$rootPath = realpath(dirname(dirname(__FILE__)));

// Add library and modules folder to the include path
set_include_path($rootPath . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';

// registers Zend_Loader_Autoloader as autoloader
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('My_');

try {
	$options = new Zend_Console_Getopt(
					array(
						'environment|e=w'	=> 'Environment name (string) required', 
						'job|j=w'	=> 'Job name (string) required'
					)
				);

	$options->parse();

	if (!$options->getOption('e') || !$options->getOption('j'))
	{
		echo $options->getUsageMessage();
		exit;
	}

	// setup environment name based on -e param
	My_Core::setEnvironmentName($options->getOption('e'));

	My_Core::init($rootPath, false);

	// no no, no no nono, no no nono, nono there's no limiit!! 
	set_time_limit(0);

	// set cron param
	My_Core::getFront()->setParam('__cron', true);

	// add job name as param
	My_Core::getFront()->setParam('__cron_job', $options->getOption('j'));

	// register cron plugin + add additional options
	$plugin = new My_Plugin_Cron();
	$plugin->setOptions($options->getRemainingArgs());
	My_Core::getFront()->registerPlugin($plugin);

	// dispatch
	My_Core::dispatch();

} catch (Zend_Console_Getopt_Exception $e) {
	echo $e->getUsageMessage();
	exit;
}