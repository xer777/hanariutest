<?php namespace Core;

class UTF8 {

	public static $server_utf8 = FALSE;
	public static $called = array();

	public static function is_mbstring()
	{
		return \Core\UTF8::$server_utf8;
	}

	public static function clean($var, $charset = NULL)
	{
		if ( ! $charset)
		{
			$charset = \Hanariu::$charset;
		}

		if (\is_array($var) OR \is_object($var))
		{
			foreach ($var as $key => $val)
			{
				$var[self::clean($key)] = self::clean($val);
			}
		}
		elseif (\is_string($var) AND $var !== '')
		{
			$var = self::strip_ascii_ctrl($var);

			if ( ! self::is_ascii($var))
			{
				$error_reporting = \error_reporting(~E_NOTICE);
				$var = \iconv($charset, $charset.'//IGNORE', $var);
				error_reporting($error_reporting);
			}
		}

		return $var;
	}

	public static function is_ascii($str)
	{
		if (\is_array($str))
		{
			$str = \implode($str);
		}

		return ! \preg_match('/[^\x00-\x7F]/S', $str);
	}

	public static function strip_ascii_ctrl($str)
	{
		return \preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}

	public static function strip_non_ascii($str)
	{
		return \preg_replace('/[^\x00-\x7F]+/S', '', $str);
	}

	public static function transliterate_to_ascii($str, $case = 0)
	{
		return \Core\UTF8\UTF8_TransliterateToAscii::_transliterate_to_ascii($str, $case);
	}

	public static function strlen($str)
	{
		if (\Core\UTF8::$server_utf8)
			return \mb_strlen($str, \Hanariu::$charset);

		return \Core\UTF8\UTF8_Strlen::_strlen($str);
	}

	public static function strpos($str, $search, $offset = 0)
	{
		if (\Core\UTF8::$server_utf8)
			return \mb_strpos($str, $search, $offset, \Hanariu::$charset);

		return \Core\UTF8\UTF8_Strpos::_strpos($str, $search, $offset);
	}

	public static function strrpos($str, $search, $offset = 0)
	{
		if (\Core\UTF8::$server_utf8)
			return \mb_strrpos($str, $search, $offset, \Hanariu::$charset);

		return \Core\UTF8\UTF8_Strrpos::_strrpos($str, $search, $offset);
	}

	public static function substr($str, $offset, $length = NULL)
	{
		if (\Core\UTF8::$server_utf8)
			return ($length === NULL)
				? \mb_substr($str, $offset, \mb_strlen($str), \Hanariu::$charset)
				: \mb_substr($str, $offset, $length, \Hanariu::$charset);

		return \Core\UTF8\UTF8_Substr::_substr($str, $offset, $length);
	}

	public static function substr_replace($str, $replacement, $offset, $length = NULL)
	{
		return \Core\UTF8\UTF8_SubstrReplace::_substr_replace($str, $replacement, $offset, $length);
	}

	public static function strtolower($str)
	{
		if (\Core\UTF8::$server_utf8)
			return \mb_strtolower($str, \Hanariu::$charset);

		return \Core\UTF8\UTF8_Strtolower::_strtolower($str);
	}

	public static function strtoupper($str)
	{
		if (\Core\UTF8::$server_utf8)
			return \mb_strtoupper($str, \Hanariu::$charset);

		return \Core\UTF8\UTF8_Strtoupper::_strtoupper($str);
	}

	public static function ucfirst($str)
	{
		return \Core\UTF8\UTF8_Ucfirst::_ucfirst($str);
	}

	public static function ucwords($str)
	{
		return \Core\UTF8\UTF8_Ucwords::_ucwords($str);
	}

	public static function strcasecmp($str1, $str2)
	{
		return \Core\UTF8\UTF8_Strcasecmp::_strcasecmp($str1, $str2);
	}

	public static function str_ireplace($search, $replace, $str, & $count = NULL)
	{
		return \Core\UTF8\UTF8_StrIreplace::_str_ireplace($search, $replace, $str, $count);
	}

	public static function stristr($str, $search)
	{
		return \Core\UTF8\UTF8_Stristr::_stristr($str, $search);
	}

	public static function strspn($str, $mask, $offset = NULL, $length = NULL)
	{
		return \Core\UTF8\UTF8_Strspn::_strspn($str, $mask, $offset, $length);
	}

	public static function strcspn($str, $mask, $offset = NULL, $length = NULL)
	{
		return \Core\UTF8\UTF8_Strcspn::_strcspn($str, $mask, $offset, $length);
	}

	public static function str_pad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT)
	{
		return \Core\UTF8\UTF8_StrPad::_str_pad($str, $final_str_length, $pad_str, $pad_type);
	}


	public static function str_split($str, $split_length = 1)
	{
		return \Core\UTF8\UTF8_StrSplit::_str_split($str, $split_length);
	}


	public static function strrev($str)
	{
		return \Core\UTF8\UTF8_Strrev::_strrev($str);
	}

	public static function trim($str, $charlist = NULL)
	{
		return \Core\UTF8\UTF8_Trim::_trim($str, $charlist);
	}


	public static function ltrim($str, $charlist = NULL)
	{
		return \Core\UTF8\UTF8_Ltrim::_ltrim($str, $charlist);
	}


	public static function rtrim($str, $charlist = NULL)
	{
		return \Core\UTF8\UTF8_Rtrim::_rtrim($str, $charlist);
	}

	public static function ord($chr)
	{
		return \Core\UTF8\UTF8_Ord::_ord($chr);
	}


	public static function to_unicode($str)
	{
		return \Core\UTF8\UTF8_ToUnicode::_to_unicode($str);
	}

	public static function from_unicode($arr)
	{
		return \Core\UTF8\UTF8_FromUnicode::_from_unicode($arr);
	}

}

if (UTF8::$server_utf8 === FALSE)
{
	// Determine if this server supports UTF-8 natively
	\Core\UTF8::$server_utf8 = \extension_loaded('mbstring');
}
