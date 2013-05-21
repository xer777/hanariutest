<?php namespace Core;

class I18n {

	public static $lang = 'en';
	public static $source = 'en';
	protected static $_cache = array();

	public static function lang($lang = NULL)
	{
		if ($lang)
		{
			\Core\I18n::$lang = \strtolower(\str_replace(array(' ', '_'), '-', $lang));
		}

		return \Core\I18n::$lang;
	}

	public static function get($string, $lang = NULL)
	{
		if ( ! $lang)
		{
			$lang = \Core\I18n::$lang;
		}

		$table = \Core\I18n::load($lang);
		return isset($table[$string]) ? $table[$string] : $string;
	}

	public static function load($lang)
	{
		if (isset(\Core\I18n::$_cache[$lang]))
		{
			return \Core\I18n::$_cache[$lang];
		}

		$table = array();
		$parts = \explode('-', $lang);

		do
		{
			$path = \implode(DIRECTORY_SEPARATOR, $parts);

			if ($files = \Hanariu::find_file('i18n', $path, NULL, TRUE))
			{
				$t = array();
				foreach ($files as $file)
				{
					$t = \array_merge($t, \Hanariu::load($file));
				}

				$table += $t;
			}

			\array_pop($parts);
		}
		while ($parts);
		return \Core\I18n::$_cache[$lang] = $table;
	}

} 
if ( ! function_exists('__'))
{
	function __($string, array $values = NULL, $lang = 'en')
	{
		if ($lang !== \Core\I18n::$lang)
		{
			$string = \Core\I18n::get($string);
		}

		return empty($values) ? $string : \strtr($string, $values);
	}
}

