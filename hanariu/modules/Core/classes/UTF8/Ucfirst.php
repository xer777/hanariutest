<?php namespace Core;

class UTF8_Ucwords{

	public static function _ucfirst($str)
	{
		if (\Core\UTF8::is_ascii($str))
			return \ucfirst($str);

		\preg_match('/^(.?)(.*)$/us', $str, $matches);
		return \Core\UTF8::strtoupper($matches[1]).$matches[2];
	}

}
