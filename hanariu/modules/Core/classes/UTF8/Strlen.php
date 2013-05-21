<?php namespace Core;

class UTF8_Strlen{

	public static function _strlen($str)
	{
		if (\Core\UTF8::is_ascii($str))
			return \strlen($str);

		return \strlen(\utf8_decode($str));
	}

}
