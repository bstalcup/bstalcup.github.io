<?php

class My_Helper_GetImage extends Zend_View_Helper_Abstract
{
	public function getImage($imageName, $moduleName = null)
	{
		if ($moduleName === null)
			return My_Core::getSkinImagePath() . $imageName;

		return My_Core::getModuleImagePath($moduleName) . $imageName;
	}
}