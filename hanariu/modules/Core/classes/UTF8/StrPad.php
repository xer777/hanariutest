<?php namespace Core;

class UTF8_StrPad{

	public static function _str_pad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT)
	{
		if (\Core\UTF8::is_ascii($str) AND \Core\UTF8::is_ascii($pad_str))
			return \str_pad($str, $final_str_length, $pad_str, $pad_type);

		$str_length = \Core\UTF8::strlen($str);

		if ($final_str_length <= 0 OR $final_str_length <= $str_length)
			return $str;

		$pad_str_length = \Core\UTF8::strlen($pad_str);
		$pad_length = $final_str_length - $str_length;

		if ($pad_type == STR_PAD_RIGHT)
		{
			$repeat = \ceil($pad_length / $pad_str_length);
			return \Core\UTF8::substr($str.\str_repeat($pad_str, $repeat), 0, $final_str_length);
		}

		if ($pad_type == STR_PAD_LEFT)
		{
			$repeat = \ceil($pad_length / $pad_str_length);
			return \Core\UTF8::substr(\str_repeat($pad_str, $repeat), 0, \floor($pad_length)).$str;
		}

		if ($pad_type == STR_PAD_BOTH)
		{
			$pad_length /= 2;
			$pad_length_left = \floor($pad_length);
			$pad_length_right = \ceil($pad_length);
			$repeat_left = \ceil($pad_length_left / $pad_str_length);
			$repeat_right = \ceil($pad_length_right / $pad_str_length);

			$pad_left = \Core\UTF8::substr(\str_repeat($pad_str, $repeat_left), 0, $pad_length_left);
			$pad_right = \Core\UTF8::substr(\str_repeat($pad_str, $repeat_right), 0, $pad_length_right);
			return $pad_left.$str.$pad_right;
		}

		throw new \Core_Exception("UTF8::str_pad: Unknown padding type (:pad_type)", array(
				':pad_type' => $pad_type,
			));
	}

}
