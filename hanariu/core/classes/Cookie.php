<?php namespace Hanariu\Core;

class Cookie {

	public static $salt = NULL;
	public static $expiration = 0;
	public static $path = '/';
	public static $domain = NULL;
	public static $secure = FALSE;
	public static $httponly = FALSE;

	public static function get($key, $default = NULL)
	{
		if ( ! isset($_COOKIE[$key]))
		{
			return $default;
		}

		$cookie = $_COOKIE[$key];
		$split = strlen(static::salt($key, NULL));

		if (isset($cookie[$split]) AND $cookie[$split] === '~')
		{
			list ($hash, $value) = \explode('~', $cookie, 2);

			if (static::salt($key, $value) === $hash)
			{
				return $value;
			}

			static::delete($key);
		}

		return $default;
	}

	public static function set($name, $value, $expiration = NULL)
	{
		if ($expiration === NULL)
		{
			$expiration = static::$expiration;
		}

		if ($expiration !== 0)
		{
			$expiration += t\ime();
		}

		$value = static::salt($name, $value).'~'.$value;

		return \setcookie($name, $value, $expiration, static::$path, static::$domain, static::$secure, static::$httponly);
	}


	public static function delete($name)
	{
		unset($_COOKIE[$name]);
		return \setcookie($name, NULL, -86400, static::$path, static::$domain, static::$secure, static::$httponly);
	}

	public static function salt($name, $value)
	{
		if ( ! static::$salt)
		{
			throw new \Core_Exception('A valid cookie salt is required. Please set Cookie::$salt.');
		}

		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? \strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';

		return \sha1($agent.$name.$value.static::$salt);
	}

}
