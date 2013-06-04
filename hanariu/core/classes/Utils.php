<?php namespace Hanariu\Core;

class Utils {

	public static $timestamp_format = 'Y-m-d H:i:s';
	public static $timezone;
	public static $server_utf8 = FALSE;

	public static $byte_units = array
	(
		'B'   => 0,
		'K'   => 10,
		'Ki'  => 10,
		'KB'  => 10,
		'KiB' => 10,
		'M'   => 20,
		'Mi'  => 20,
		'MB'  => 20,
		'MiB' => 20,
		'G'   => 30,
		'Gi'  => 30,
		'GB'  => 30,
		'GiB' => 30,
		'T'   => 40,
		'Ti'  => 40,
		'TB'  => 40,
		'TiB' => 40,
		'P'   => 50,
		'Pi'  => 50,
		'PB'  => 50,
		'PiB' => 50,
		'E'   => 60,
		'Ei'  => 60,
		'EB'  => 60,
		'EiB' => 60,
		'Z'   => 70,
		'Zi'  => 70,
		'ZB'  => 70,
		'ZiB' => 70,
		'Y'   => 80,
		'Yi'  => 80,
		'YB'  => 80,
		'YiB' => 80,
	);

	public static function ucfirst($string, $delimiter = '-')
	{
		return \implode($delimiter, \array_map('ucfirst', \explode($delimiter, $string)));
	}

	public static function bytes($size)
	{
		$size = \trim( (string) $size);
		$accepted = \implode('|', \array_keys(static::$byte_units));
		$pattern = '/^([0-9]+(?:\.[0-9]+)?)('.$accepted.')?$/Di';
		if ( ! \preg_match($pattern, $size, $matches))
			throw new \Exception('The byte unit size, ":size", is improperly formatted.', array(
				':size' => $size,
			));

		$size = (float) $matches[1];
		$unit = \Arr::get($matches, 2, 'B');
		$bytes = $size * \pow(2, static::$byte_units[$unit]);
		return $bytes;
	}

	public static function mime_by_ext($extension)
	{
		$mimes = \Hanariu::$config->load('mimes');

		return isset($mimes[$extension]) ? ( (array) $mimes[$extension]) : array();
	}

	public static function formatted_time($datetime_str = 'now', $timestamp_format = NULL, $timezone = NULL)
	{
		$timestamp_format = ($timestamp_format == NULL) ? static::$timestamp_format : $timestamp_format;
		$timezone         = ($timezone === NULL) ? static::$timezone : $timezone;

		$tz   = new \DateTimeZone($timezone ? $timezone : \date_default_timezone_get());
		$time = new \DateTime($datetime_str, $tz);

		if ($time->getTimeZone()->getName() !== $tz->getName())
		{
			$time->setTimeZone($tz);
		}

		return $time->format($timestamp_format);
	}

	public static function chars($value, $double_encode = TRUE)
	{
		return \htmlspecialchars( (string) $value, ENT_QUOTES, \Hanariu::$charset, $double_encode);
	}

	//UTF8
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
				$var[static::clean($key)] = static::clean($val);
			}
		}
		elseif (\is_string($var) AND $var !== '')
		{
			$var = static::strip_ascii_ctrl($var);

			if ( ! static::is_ascii($var))
			{
				$error_reporting = \error_reporting(~E_NOTICE);
				$var = \iconv($charset, $charset.'//IGNORE', $var);
				error_reporting($error_reporting);
			}
		}

		return $var;
	}

	public static function strip_ascii_ctrl($str)
	{
		return \preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}

	public static function is_ascii($str)
	{
		if (\is_array($str))
		{
			$str = \implode($str);
		}

		return ! \preg_match('/[^\x00-\x7F]/S', $str);
	}

	public static function strlen($str)
	{
		if (static::$server_utf8)
			return \mb_strlen($str, \Hanariu::$charset);

		if (static::is_ascii($str))
			return \strlen($str);

		return \strlen(\utf8_decode($str));
	}
	//UTF8 end

	public static function user_agent($agent, $value)
	{
		if (is_array($value))
		{
			$data = array();
			foreach ($value as $part)
			{
				$data[$part] = static::user_agent($agent, $part);
			}

			return $data;
		}

		if ($value === 'browser' OR $value == 'version')
		{
			$info = array();
			$browsers = \Hanariu::$config->load('user_agents')->browser;

			foreach ($browsers as $search => $name)
			{
				if (\stripos($agent, $search) !== FALSE)
				{
					$info['browser'] = $name;

					if (\preg_match('#'.\preg_quote($search).'[^0-9.]*+([0-9.][0-9.a-z]*)#i', $agent, $matches)) // \Request::$user_agent, $matches))
					{
						$info['version'] = $matches[1];
					}
					else
					{
						$info['version'] = FALSE;
					}

					return $info[$value];
				}
			}
		}
		else
		{
			$group = \Hanariu::$config->load('user_agents')->$value;

			foreach ($group as $search => $name)
			{
				if (\stripos($agent, $search) !== FALSE)
				{

					return $name;
				}
			}
		}

		return FALSE;
	}
}
