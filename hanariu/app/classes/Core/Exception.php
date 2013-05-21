<?php

class Core_Exception extends \Hanariu\Core\Core_Exception { //\Hanariu\Core\


	public static $error_view = 'errors/error';
	public static $error_view_content_type = 'text/html';

	/*public function __construct($message = "", array $variables = NULL, $code = 0, \Exception $previous = NULL)
	{
		empty($variables) ? $message : $message = \strtr($message, $variables);
		parent::__construct($message, (int) $code, $previous);
		$this->code = $code;
	}*/

	public static function response(\Exception $e)
	{
		try
		{
			$class   = \get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();
			$trace   = $e->getTrace();

			if ( ! \headers_sent())
			{
				$http_header_status = ($e instanceof \HTTP\Exception) ? $code : 500;
			}

			if ($e instanceof \HTTP\Exception AND $trace[0]['function'] == 'factory')
			{
				\extract(\array_shift($trace));
			}


			if ($e instanceof \ErrorException)
			{

				if (\function_exists('xdebug_get_function_stack') AND $code == E_ERROR)
				{
					$trace = \array_slice(\array_reverse(\xdebug_get_function_stack()), 4);

					foreach ($trace as & $frame)
					{

						if ( ! isset($frame['type']))
						{
							$frame['type'] = '??';
						}

						if (isset($frame['params']) AND ! isset($frame['args']))
						{
							$frame['args'] = $frame['params'];
						}
					}
				}
				
				if (isset(\Core_Exception::$php_errors[$code]))
				{
					$code = \Core_Exception::$php_errors[$code];
				}
			}


			if (\defined('PHPUnit_MAIN_METHOD'))
			{
				$trace = \array_slice($trace, 0, 2);
			}

			$view = \Core\View::factory(\Core_Exception::$error_view, \get_defined_vars());
			$response = \Response::factory();
			$response->status(($e instanceof \HTTP\Exception) ? $e->getCode() : 500);
			$response->headers('Content-Type', \Core_Exception::$error_view_content_type.'; charset='.\Hanariu::$charset);
			$response->body($view->render());
		}
		catch (Exception $e)
		{
			$response = \Response::factory();
			$response->status(500);
			$response->headers('Content-Type', 'text/plain');
			$response->body(\Core_Exception::text($e));
		}

		return $response;
	}

}