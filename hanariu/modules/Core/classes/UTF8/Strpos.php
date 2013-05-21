<?php namespace Core;

class UTF8_Strpos{

	public static function _strpos($str, $search, $offset = 0)
	{
		$offset = (int) $offset;

		if (\Core\UTF8::is_ascii($str) AND \Core\UTF8::is_ascii($search))
			return \strpos($str, $search, $offset);

		if ($offset == 0)
		{
			$array = \explode($search, $str, 2);
			return isset($array[1]) ? \Core\UTF8::strlen($array[0]) : FALSE;
		}

		$str = \Core\UTF8::substr($str, $offset);
		$pos = \Core\UTF8::strpos($str, $search);
		return ($pos === FALSE) ? FALSE : ($pos + $offset);
	}

}
