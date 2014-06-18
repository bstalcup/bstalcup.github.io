<?php

/**
 * My user role db table abstraction object.
 * 
 * @access public
 */
class My_User_Role extends Zend_Db_Table
{
	protected $_name = 'user_role';
	protected $_primary = 'id';
	protected $_rowClass = 'My_User_Role_Row';

	private static $__instance = null;

	public static function getInstance()
	{
		if (self::$__instance === null)
			self::$__instance = new self();
		return self::$__instance;
	}
}
