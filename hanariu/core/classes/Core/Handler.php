<?php namespace Hanariu\Core;

class Core_Handler {

	public static function error_handler($code, $error, $file = NULL, $line = NULL)
	{
		\Autoloader::_reset();
		if (\error_reporting() & $code)
		{
			throw new \ErrorException($error, $code, 0, $file, $line);
		}
		return TRUE;
	}

	public static function shutdown_handler()
	{
		\Autoloader::_reset();

		if ( ! \Hanariu::$_init)
		{
			return;
		}

		try
		{
			if (\Hanariu::$caching === TRUE AND \Hanariu::$_files_changed === TRUE)
			{

				\Hanariu::cache('Hanariu::find_file()', \Hanariu::$_files);
			}
		}
		catch (\Exception $e)
		{
			\Core_Exception::handler($e);
		}

		if (\Hanariu::$errors AND $error = \error_get_last() AND \in_array($error['type'], \Hanariu::$shutdown_errors))
		{
			\ob_get_level() AND \ob_clean();
			\Core_Exception::handler(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
			exit(1);
		}
	}

}
