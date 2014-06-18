<?php

class My_Helper_Truncate extends Zend_View_Helper_Abstract
{
	public function truncate($string, $length = 180, $separator = '...', $maxOver = 10)
	{
		if (strlen($string) <= $length) return $string;
		$end = strpos($string, ' ', $length) ? strpos($string, ' ', $length) : strlen($string);
		if ($end > $length + $maxOver)
			$end = intval($length + ($maxOver / 2));
		return substr($string, 0, $end) . $separator;
	}
}