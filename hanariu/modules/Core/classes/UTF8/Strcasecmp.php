<?php namespace Core;

class UTF8_Strcasecmp{

	public static function _strcasecmp($str1, $str2)
	{
		if (\Core\UTF8::is_ascii($str1) AND \Core\UTF8::is_ascii($str2))
			return \strcasecmp($str1, $str2);

		$str1 = \Core\UTF8::strtolower($str1);
		$str2 = \Core\UTF8::strtolower($str2);
		return \strcmp($str1, $str2);
	}

}
