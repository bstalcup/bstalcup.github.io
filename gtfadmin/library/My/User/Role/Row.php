<?php

/**
 * My user role db row abstraction object.
 * 
 * @access public
 */
class My_User_Role_Row extends Zend_Db_Table_Row
{
	public function __wakeup()
	{
		$this->setTable(My_User_Role::getInstance());
	}

	public function getParentRole()
	{
		if (!$this->parent) 
			return null;

		return $this->getTable()->find($this->parent)->current();
	}
}
