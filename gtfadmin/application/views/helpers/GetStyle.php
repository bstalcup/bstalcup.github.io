<?php

class My_Helper_GetStyle extends Zend_View_Helper_Abstract
{
	public function getStyle($imageName, $moduleName = null, $cachePrefix = true)
	{
		if ($moduleName === null)
			return My_Core::getSkinStylePath() . $imageName . ($cachePrefix ? '?_cache=' . My_Core::getConfig()->cache_frontend->options->cache_id_prefix : '');

		return My_Core::getModuleStylePath($moduleName) . $imageName . ($cachePrefix ? '?_cache=' . My_Core::getConfig()->cache_frontend->options->cache_id_prefix : '');
	}
}