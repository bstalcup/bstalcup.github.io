<?php

class My_Helper_GetJs extends Zend_View_Helper_Abstract
{
	public function getJs($imageName, $moduleName = null, $cachePrefix = true)
	{
		if ($moduleName === null)
			return My_Core::getSkinJsPath() . $imageName . ($cachePrefix ? '?_cache=' . My_Core::getConfig()->cache_frontend->options->cache_id_prefix : '');

		return My_Core::getModuleJsPath($moduleName) . $imageName . ($cachePrefix ? '?_cache=' . My_Core::getConfig()->cache_frontend->options->cache_id_prefix : '');
	}
}