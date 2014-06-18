<?php

class My_Helper_GetSwf extends Zend_View_Helper_Abstract
{
	public function getSwf($imageName, $moduleName = null)
	{
		if ($moduleName === null)
			return My_Core::getSkinSwfPath() . $imageName;

		return My_Core::getModuleSwfPath($moduleName) . $imageName;
	}
}