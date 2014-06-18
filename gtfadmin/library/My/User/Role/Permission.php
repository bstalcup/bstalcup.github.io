<?php

/**
 * My user role db table abstraction object.
 * 
 * @access public
 */
class My_User_Role_Permission extends Zend_Db_Table
{
	protected $_name = 'user_role_permission';
	protected $_primary = 'id';

	private static $__instance = null;

	public static function getInstance()
	{
		if (self::$__instance === null)
			self::$__instance = new self();
		return self::$__instance;
	}
}

