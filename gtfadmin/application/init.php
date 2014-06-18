<?php

// pre-init setup
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
set_time_limit(10);

// setup root path
$rootPath = realpath(dirname(dirname(__FILE__)));

// Add library and modules folder to the include path
set_include_path($rootPath . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';

// registers Zend_Loader_Autoloader as autoloader
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('My_');

// initialize the application and dispatch to proper controller @see My_Core::init()
My_Core::init($rootPath);

