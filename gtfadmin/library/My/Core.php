<?php

/**
 * My core library.
 *
 * @access		public
 */
final class My_Core 
{
	/**
	 * Global configuration.
	 *
	 * @var Zend_Config_Xml
	 */
	private static $__globalConfig = null;

	/**
	 * Application configuration.
	 *
	 * @var Zend_Config_Ini
	 * @see application/config/{environment_name}.ini
	 */
	private static $__config = null;

	/**
	 * Main router configuration.
	 *
	 * @var Zend_Config_Ini
	 * @see application/config/routes.ini
	 */
	private static $__routerConfig = null;

	/**
	 * Current environment name.
	 *
	 * @var String
	 */
	private static $__environmentName = null;

	/**
	 * Application path relative to root path.
	 *
	 * @var String
	 */
	private static $__applicationPath = 'application/';

	/**
	 * Application root path.
	 *
	 * @var String
	 */
	private static $__rootPath = null;

	/**
	 * Default logging object.
	 *
	 * @var Zend_Log
	 */
	private static $__logger = null;

	/**
	 * Default debugging object.
	 *
	 * @var Zend_Log
	 */
	private static $__debugger = null;

	/**
	 * Default cache object.
	 *
	 * @var Zend_Cache
	 */
	private static $__cache = null;

	/**
	 * Default application database.
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	private static $__database = null;

	/**
	 * Default translate object.
	 *
	 * @var Zend_Translate
	 */
	private static $__translate = null;

	/**
	 * Default locale object.
	 *
	 * @var Zend_Locale
	 */
	private static $__locale = null;

	/**
	 * Default application front object.
	 *
	 * @var Zend_Controller_Front
	 */
	private static $__front = null;

	/**
	 * Application dispatched flag.
	 *
	 * @var Bool
	 */
	private static $__dispatched = false;

	/**
	 * Application ACL object.
	 *
	 * @var Zend_Acl
	 */
	private static $__acl = null;

	/**
	 * Application user.
	 *
	 * @var My_User
	 */
	private static $__user = null;

	/**
	 * Application init.
	 * Adds modules. Registers the core plugin. starts session
	 * Sets up default adapter for database transactions.
	 * Sets up layout system.
	 * Registers routes.
	 * 
	 * @return void
	 */
	public static function init($rootPath, $dispatch = true)
	{
		// setup default route
		self::$__rootPath = $rootPath;

		// set default timezone to avoid notices
		date_default_timezone_set(self::getConfig()->locale->timezone);

		// set application memory limit
		ini_set('memory_limit', self::getConfig()->application->memory);

		// setup translator
		Zend_Registry::set("Zend_Translate", self::getTranslate());

		// add module directory to front controller
		self::getFront()->addModuleDirectory(self::getModulesPath());

		// register default plugin
		self::getFront()->registerPlugin(new My_Plugin_Default());

		// register auth plugin
		self::getFront()->registerPlugin(new My_Plugin_Auth());

		// register default namespace for plugins
		Zend_Controller_Action_HelperBroker::addPath(self::getHelpersPath(), 'My_Helper');

		// setup MVC environment
		Zend_Layout::startMvc(self::getLayoutScriptPath());

		// Route names
		foreach (self::getRouterConfig()->routes as $routeName => $routeData)
			self::getRouterConfig()->routes->{$routeName}->defaults->route_name = $routeName;

		// Routes
		self::getFront()->getRouter()
							->removeDefaultRoutes()
							->addConfig(self::getRouterConfig(), 'routes');

		// error reporting
		if (self::getConfig()->debug->level == 0) error_reporting(0);
		if (self::getConfig()->debug->level == 1) error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

		// check for session ID and set if found
		if (isset($_GET['PHPSESSID']) && trim($_GET['PHPSESSID'])) {
			Zend_Session::setId($_GET['PHPSESSID']);
		}

		// start session
		Zend_Session::start();

		// set session as adapter for Zend_Auth storage.
		Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('userNamespace'));

		// dispatch control to front if requested
		if ($dispatch) self::dispatch();
	}

	/**
	 * Dispatches request to Zend_Controller_Front
	 * 
	 * @return void
	 */
	public static function dispatch()
	{
		if (self::$__dispatched === true)
			throw new My_Exception('Application is already dispatched.');

		// set dispatch and pass control to the front
		self::$__dispatched = true;

		self::getFront()->dispatch();
	}


	/**
	 * Get environment name.
	 * 
	 * @return String environment name
	 */
	public static function getEnvironmentName()
	{
		if (self::$__environmentName === null)
		{
			foreach (self::getGlobalConfig()->website->environments as $environmentName => $environmentConfig)
			{
				foreach ($environmentConfig->hosts as $hostType => $hostUrl)
				{
					if ($hostUrl == $_SERVER['SERVER_NAME'])
					{
						if ($hostType == 'alias' && $hostUrl != $environmentConfig->hosts->default)
						{
							header('Location: http://' . $environmentConfig->hosts->default);
							die();
						}
						self::$__environmentName = $environmentName;
					}
				}
			}
		}
		return self::$__environmentName;
	}

	/**
	 * Set environment name.
	 * 
	 * @return String environment name
	 */
	public static function setEnvironmentName($environmentName)
	{
		self::$__environmentName = $environmentName;
		return self::$__environmentName;
	}

	/**
	 * Get environment config.
	 * 
	 * @return Zend_Config_Xml environment name
	 */
	public static function getEnvironmentConfig()
	{
		if (!isset(self::getGlobalConfig()->website->environments->{self::getEnvironmentName()}))
			throw new My_Exception('Cannot find environment');
		return self::getGlobalConfig()->website->environments->{self::getEnvironmentName()};
	}

	/**
	 * Returns root path ( __ROOT__ )
	 * 
	 * @return String
	 */
	public static function getRootPath()
	{
		return self::$__rootPath;
	}

	/**
	 * Returns application path ( __APP__ )
	 * 
	 * @return String
	 */
	public static function getApplicationPath()
	{
		return self::getRootPath() . DIRECTORY_SEPARATOR . self::$__applicationPath;
	}

	/**
	 * Returns application URI
	 * 
	 * @return String
	 */
	public static function getApplicationUri()
	{
		return self::getFront()->getRequest()->getScheme() . '://' . self::getEnvironmentConfig()->hosts->default;
	}

	/**
	 * Returns visitor IP
	 * 
	 * @return String
	 */
	public static function getIp()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		return $ip;
	}

	/**
	 * Returns visitor User Agent
	 * 
	 * @return String
	 */
	public static function getAgent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	 * Returns application name
	 * 
	 * @return String
	 */
	public static function getName()
	{
		return self::getConfig()->application->name;
	}

	/**
	 * Returns global ACL object. If it's not available, it loads it and stores it for future use.
	 * 
	 * @return Zend_Acl
	 */
	public static function getAcl()
	{
		if (self::$__acl === null)
			self::$__acl = new Zend_Acl();
		return self::$__acl;
	}

	/**
	 * Returns user object. If it's not available, it loads it based on session and stores it for future use.
	 * 
	 * @return My_User_Row
	 */
	public static function getUser()
	{
		if (self::$__user === null)
		{
			self::$__user = My_User::getLoggedInUser();

			// no user, default to guest
			if (!self::$__user)
				self::$__user = new My_User();
		}
		return self::$__user;
	}

	/**
	 * Setter for global user object.
	 * 
	 */
	public static function setUser(My_User_Row $user)
	{
		self::$__user = $user;
		return self::$__user;
	}

	/**
	 * Returns global configuration object. If it's not available, it loads and stores it for future use.
	 * 
	 * @return Zend_Config_Ini
	 */
	public static function getGlobalConfig()
	{
		if (self::$__globalConfig === null)
			self::$__globalConfig = new Zend_Config_Xml(self::getApplicationPath() . 'config/websites.xml');
		return self::$__globalConfig;
	}

	/**
	 * Returns main configuration object. If it's not available, it loads and stores it for future use.
	 * 
	 * @return Zend_Config_Ini
	 */
	public static function getConfig()
	{
		if (self::$__config === null)
		{
			if (!is_readable(self::getApplicationPath() . self::getEnvironmentConfig()->config))
				throw new My_Exception('Cannot read environment configuration');
			self::$__config = new Zend_Config_Ini(self::getApplicationPath() . self::getEnvironmentConfig()->config, null, array('allowModifications' => true));
		}
		return self::$__config;
	}

	/**
	 * Returns main router configuration object. If it's not available, it loads and stores it for future use.
	 * 
	 * @see application/config/routes.ini
	 * @return Zend_Config_Ini
	 */
	public static function getRouterConfig()
	{
		if (self::$__routerConfig === null)
		{
			if (!is_readable(self::getApplicationPath() . self::getEnvironmentConfig()->router_config))
				throw new My_Exception('Cannot read router configuration for environment ' . self::getEnvironmentName());
			self::$__routerConfig = new Zend_Config_Ini(self::getApplicationPath() . self::getEnvironmentConfig()->router_config, null, array('allowModifications' => true));
		}
		return self::$__routerConfig;
	}

	/**
	 * Parses $path string and returns all occurrences of __ROOT__ and __APP__ strings replaced with the actual root and application paths 
	 *
	 * @param String $path
	 * @return String
	 */
	public static function parsedPath($path)
	{
		return preg_replace(array('/__ROOT__/', '/__APP__/'), array(self::getRootPath() . '/', self::getApplicationPath()), $path);
	}

	/**
	 * Returns modules path.
	 *
	 * @return String
	 */
	public static function getModulesPath()
	{
		return self::parsedPath(self::getConfig()->application->modules);
	}

	/**
	 * Returns controller action helpers path.
	 *
	 * @return String
	 */
	public static function getHelpersPath()
	{
		return self::parsedPath(self::getConfig()->application->helpers);
	}

	/**
	 * Returns application temp path.
	 *
	 * @return String
	 */
	public static function getTempPath()
	{
		return self::parsedPath(self::getConfig()->application->temp_folder);
	}

	/**
	 * Returns layouts path.
	 *
	 * @return String
	 */
	public static function getLayoutScriptPath()
	{
		return self::parsedPath(self::getConfig()->layout->scripts);
	}

	/**
	 * Returns layouts pdf path.
	 *
	 * @return String
	 */
	public static function getLayoutPDFPath()
	{
		return self::parsedPath(self::getConfig()->layout->pdf);
	}

	/**
	 * Returns layout helpers path
	 *
	 * @return String
	 */
	public static function getLayoutHelperPath()
	{
		return self::parsedPath(self::getConfig()->layout->helpers);
	}

	/**
	 * Returns layout filters path.
	 *
	 * @return String
	 */
	public static function getLayoutFilterPath()
	{
		return self::parsedPath(self::getConfig()->layout->filters);
	}

	/**
	 * Returns public skin path.
	 *
	 * @return String
	 */
	public static function getSkinPath()
	{
		return self::parsedPath(self::getConfig()->skin->folder);
	}

	/**
	 * Returns skin images path.
	 *
	 * @return String
	 */
	public static function getSkinImagePath()
	{
		return self::getSkinPath() . self::getConfig()->skin->image;
	}

	/**
	 * Returns skin styles path.
	 *
	 * @return String
	 */
	public static function getSkinStylePath()
	{
		return self::getSkinPath() . self::getConfig()->skin->style;
	}

	/**
	 * Returns skin javascript path
	 *
	 * @return String
	 */
	public static function getSkinJsPath()
	{
		return self::getSkinPath() . self::getConfig()->skin->javascript;
	}

	/**
	 * Returns skin swf path
	 *
	 * @return String
	 */
	public static function getSkinSwfPath()
	{
		return self::getSkinPath() . self::getConfig()->skin->swf;
	}

	/**
	 * Returns module-specific skin image path
	 *
	 * @param String $moduleName
	 * @return String
	 */
	public static function getModuleImagePath($moduleName)
	{
		return self::getSkinPath() . self::getConfig()->skin->module . $moduleName . '/' . self::getConfig()->skin->image;
	}

	/**
	 * Returns module-specific style path
	 *
	 * @param String $moduleName
	 * @return String
	 */
	public static function getModuleStylePath($moduleName)
	{
		return self::getSkinPath() . self::getConfig()->skin->module . $moduleName . '/' . self::getConfig()->skin->style;
	}

	/**
	 * Returns module-specific javascript path.
	 *
	 * @param String $moduleName
	 * @return String
	 */
	public static function getModuleJsPath($moduleName)
	{
		return self::getSkinPath() . self::getConfig()->skin->module . $moduleName . '/' . self::getConfig()->skin->javascript;
	}

	/**
	 * Returns module-specific swf path.
	 *
	 * @param String $moduleName
	 * @return String
	 */
	public static function getModuleSwfPath($moduleName)
	{
		return self::getSkinPath() . self::getConfig()->skin->module . $moduleName . '/' . self::getConfig()->skin->swf;
	}

	/**
	 * Returns the front controller. If not available it loads and stores it for future use. 
	 *
	 * @return Zend_Controller_Front_Abstract
	 */
	public static function getFront()
	{
		if (self::$__front === null)
			self::$__front = Zend_Controller_Front::getInstance();
		return self::$__front;
	}

	/**
	 * Sets default logger object.
	 *
	 * @param Zend_Log $logger
	 * @return Zend_Log
	 */
	public static function setLogger(Zend_Log $logger)
	{
		self::$__logger = $logger;
		return self::$__logger;
	}

	/**
	 * Returns default logger object. 
	 *
	 * @return Zend_Log
	 */
	public static function getLogger()
	{
		if (self::$__logger === null)
		{
			$logger = new Zend_Log();

			$system = new Zend_Log_Writer_Stream(self::parsedPath(self::getConfig()->log->file));
			$system->addFilter(new Zend_Log_Filter_Priority(Zend_Log::EMERG));
			$logger->addWriter($system);

			$debug = new Zend_Log_Writer_Stream(self::parsedPath(self::getConfig()->debug->file));
			$debug->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG));
			$logger->addWriter($debug);

			self::$__logger = $logger;
		}
		return self::$__logger;
	}

	/**
	 * Returns default debugger object. Adds firebug as output if debug level > 0. 
	 *
	 * @return Zend_Log
	 */
	public static function getDebugger()
	{
		if (self::$__debugger === null)
		{
			$debugger = new Zend_Log();

			$firebug = new Zend_Log_Writer_Firebug();

			if (self::getConfig()->debug->level == 1)
				$firebug->addFilter(new Zend_Log_Filter_Priority(Zend_Log::EMERG));

			if (self::getConfig()->debug->level > 0) $debugger->addWriter($firebug);
				else $debugger->addWriter(new Zend_Log_Writer_Null());

			self::$__debugger = $debugger;
		}
		return self::$__debugger;
	}

	/**
	 * Returns default cache object.
	 *
	 * @return Zend_Cache
	 */
	public static function getCache()
	{
		if (self::$__cache === null)
		{
			$cacheFrontendClass = self::getConfig()->cache_frontend->type;
			$cacheFrontend = new $cacheFrontendClass(self::getConfig()->cache_frontend->options->toArray());

			$cacheBackendClass = self::getConfig()->cache_backend->type;
			$cacheBackend = new $cacheBackendClass(self::getConfig()->cache_backend->options->toArray());

			self::$__cache = Zend_Cache::factory($cacheFrontend, $cacheBackend);
		}
		return self::$__cache;
	}

	/**
	 * Returns default translate object
	 *
	 * @return Zend_Translate
	 */
	public static function getTranslate()
	{
		if (self::$__translate === null)
			self::$__translate = new Zend_Translate('csv', 
											self::parsedPath(self::getConfig()->locale->folder), 
											self::getConfig()->locale->default, 
											array('scan' => Zend_Translate::LOCALE_DIRECTORY));
		return self::$__translate;
	}

	/**
	 * Returns default locale object
	 *
	 * @return Zend_Locale
	 */
	public static function getLocale()
	{
		if (self::$__locale === null)
			self::$__locale = new Zend_Locale(self::getConfig()->locale->default);
		return self::$__locale;
	}

} // end My_Core
