<?php namespace Core;

class UTF8_Ltrim {

	public static function _ltrim($str, $charlist = NULL)
	{
		if ($charlist === NULL)
			return \ltrim($str);

		if (\Core\UTF8::is_ascii($charlist))
			return \ltrim($str, $charlist);

		$charlist = \preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);

		return \preg_replace('/^['.$charlist.']+/u', '', $str);
	}

}
