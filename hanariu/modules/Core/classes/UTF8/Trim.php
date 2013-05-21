<?php namespace Core;

class UTF8_Trim{

	public static function _trim($str, $charlist = NULL)
	{
		if ($charlist === NULL)
			return \trim($str);

		return \Core\UTF8::ltrim(\Core\UTF8::rtrim($str, $charlist), $charlist);
	}

}
