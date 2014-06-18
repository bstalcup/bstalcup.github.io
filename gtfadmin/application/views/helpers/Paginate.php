<?php

class My_Helper_Paginate extends Zend_View_Helper_Abstract
{
	public $view = null;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	// builds pages and returns the paged SQL query
	public function paginate($table, $select, $page = 1, $countField = '*', $customSelect = false)
	{
		$this->view->paginationCurrent = intval($page);
		$this->view->paginationTotal = 1;
		$this->view->paginationLimit = My_Core::getConfig()->pagination->size;
		$this->view->paginationShow = My_Core::getConfig()->pagination->show;
		if (!$this->view->paginationCurrent)
			$this->view->paginationCurrent = 1;

		if (!$customSelect)
		{
			// clear existing FROM $table condition so we can safely select just the COUNT field on $table
			$csel = clone $select;
			$parts = $csel->getPart(Zend_Db_Select::FROM);
			$cols = $csel->getPart(Zend_Db_Select::COLUMNS);
			$csel = $csel->reset(Zend_Db_Select::FROM);
			$tname = $table->info('name');
			if (count($parts))
			{
				foreach ($parts as $key => $part)
				{
					if ($part['tableName'] == $tname)
					{
						// add the count part
						$csel = $csel->from(array($key => $tname), array('__paginationTotal' => new Zend_Db_Expr('COUNT(' . $countField . ')')));
					} else {
						// add the rest of the joined tables (if any)
						$jtype = explode(' ', $part['joinType']);
						$csel = $csel->{'join' . ucfirst($jtype[0])}(array($key => $part['tableName']), $part['joinCondition'], array());
					}
				}
			} else $csel = $csel->from($tname, array('__paginationTotal' => new Zend_Db_Expr('COUNT(' . $countField . ')')));
			$totalRow = $table->fetchRow($csel);
		} else {
			$totalRow = $table->getAdapter()->fetchRow($customSelect, array(), Zend_Db::FETCH_OBJ);
		}
		if ($totalRow) $this->view->paginationTotal = ceil(intval($totalRow->__paginationTotal) / $this->view->paginationLimit);
			else $this->view->paginationTotal = 0;

		return $select->limitPage($this->view->paginationCurrent, $this->view->paginationLimit);
	}
}