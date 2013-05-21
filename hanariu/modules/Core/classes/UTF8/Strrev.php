<?php namespace Core;

class UTF8_Strrev{

	public static function _strrev($str)
	{
		if (\Core\UTF8::is_ascii($str))
			return \strrev($str);

		\preg_match_all('/./us', $str, $matches);
		return \implode('', \array_reverse($matches[0]));
	}

}
