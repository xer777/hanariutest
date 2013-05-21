<?php //namespace Core;

class URL extends \Hanariu\Core\URL {

	public static function title($title, $separator = '-', $ascii_only = FALSE)
	{
		if ($ascii_only === TRUE)
		{
			$title = \Core\UTF8\transliterate_to_ascii($title);
			$title = \preg_replace('![^'.\preg_quote($separator).'a-z0-9\s]+!', '', \strtolower($title));
		}
		else
		{
			$title = \preg_replace('![^'.\preg_quote($separator).'\pL\pN\s]+!u', '', \Core\UTF8::strtolower($title));
		}

		$title = \preg_replace('!['.\preg_quote($separator).'\s]+!u', $separator, $title);
		return \trim($title, $separator);
	}

}
