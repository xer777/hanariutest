<?php namespace Core;

class UTF8_Strrpos{

	public static function _strrpos($str, $search, $offset = 0)
	{
		$offset = (int) $offset;

		if (\Core\UTF8::is_ascii($str) AND \Core\UTF8::is_ascii($search))
			return \strrpos($str, $search, $offset);

		if ($offset == 0)
		{
			$array = \explode($search, $str, -1);
			return isset($array[0]) ? \Core\UTF8::strlen(\implode($search, $array)) : FALSE;
		}

		$str = \Core\UTF8::substr($str, $offset);
		$pos = \Core\UTF8::strrpos($str, $search);
		return ($pos === FALSE) ? FALSE : ($pos + $offset);
	}

}
