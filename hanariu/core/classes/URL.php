<?php namespace Hanariu\Core;

class URL {


	public static function base($protocol = NULL, $index = FALSE)
	{
		$base_url = \Hanariu::$base_url;

		if ($protocol === TRUE)
		{
			$protocol = \Request::$initial;
		}

		if ($protocol instanceof \Request)
		{
			if ( ! $protocol->secure())
			{
				list($protocol) = \explode('/', \strtolower($protocol->protocol()));
			}
			else
			{
				$protocol = 'https';
			}
		}

		if ( ! $protocol)
		{
			$protocol = \parse_url($base_url, PHP_URL_SCHEME);
		}

		if ($index === TRUE AND ! empty(\Hanariu::$index_file))
		{
			$base_url .= \Hanariu::$index_file.'/';
		}

		if (\is_string($protocol))
		{
			if ($port = \parse_url($base_url, PHP_URL_PORT))
			{
				$port = ':'.$port;
			}

			if ($domain = \parse_url($base_url, PHP_URL_HOST))
			{
				$base_url = \parse_url($base_url, PHP_URL_PATH);
			}
			else
			{
				$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
			}

			$base_url = $protocol.'://'.$domain.$port.$base_url;
		}

		return $base_url;
	}


	public static function site($uri = '', $protocol = NULL, $index = TRUE)
	{
		$path = \preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));

		if ( ! \Utils::is_ascii($path))
		{
			$path = \preg_replace_callback('~([^/]+)~', 'URL::_rawurlencode_callback', $path);
		}

		return static::base($protocol, $index).$path;
	}

	protected static function _rawurlencode_callback($matches)
	{
		return \rawurlencode($matches[0]);
	}


	public static function query(array $params = NULL, $use_get = TRUE)
	{
		if ($use_get)
		{
			if ($params === NULL)
			{
				$params = $_GET;
			}
			else
			{
				$params = \Arr::merge($_GET, $params);
			}
		}

		if (empty($params))
		{
			return '';
		}

		$query = \http_build_query($params, '', '&');

		return ($query === '') ? '' : ('?'.$query);
	}


	public static function title($title, $separator = '-', $ascii_only = FALSE)
	{
		// replace non letter or digits by separator
		$text = \preg_replace('~[^\\pL\d]+~u', $separator, $title);

		// trim
		$title = \trim($title, $separator);

		// transliterate
		$title = \iconv('utf-8', 'us-ascii//TRANSLIT', $title);

		// lowercase
		$title = \strtolower($title);

		// remove unwanted characters
		$title = \preg_replace('~[^-\w]+~', '', $title);

		if (empty($title))
		{
			return 'n-a';
		}

		return $title;
	}

}
