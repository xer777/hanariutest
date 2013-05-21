<?php namespace Hanariu\Core;

class Log {

	const EMERGENCY = LOG_EMERG;    // 0
	const ALERT     = LOG_ALERT;    // 1
	const CRITICAL  = LOG_CRIT;     // 2
	const ERROR     = LOG_ERR;      // 3
	const WARNING   = LOG_WARNING;  // 4
	const NOTICE    = LOG_NOTICE;   // 5
	const INFO      = LOG_INFO;     // 6
	const DEBUG     = LOG_DEBUG;    // 7

	public static $write_on_add = FALSE;
	protected static $_instance;

	public static function instance()
	{
		if (\Log::$_instance === NULL)
		{
			\Log::$_instance = new \Log;
			\register_shutdown_function(array(\Log::$_instance, 'write'));
		}

		return \Log::$_instance;
	}

	protected $_messages = array();
	protected $_writers = array();

	public function attach(\Log_Writer $writer, $levels = array(), $min_level = 0)
	{
		if ( ! \is_array($levels))
		{
			$levels = \range($min_level, $levels);
		}
		
		$this->_writers["{$writer}"] = array
		(
			'object' => $writer,
			'levels' => $levels
		);

		return $this;
	}

	public function detach(\Log_Writer $writer)
	{
		// Remove the writer
		unset($this->_writers["{$writer}"]);

		return $this;
	}

	public function add($level, $message, array $values = NULL, array $additional = NULL)
	{
		if ($values)
		{
			$message = \strtr($message, $values);
		}

		if (isset($additional['exception']))
		{
			$trace = $additional['exception']->getTrace();
		}
		else
		{
			if ( ! \defined('DEBUG_BACKTRACE_IGNORE_ARGS'))
			{
				$trace = \array_map(function ($item) {
					unset($item['args']);
					return $item;
				}, \array_slice(\debug_backtrace(FALSE), 1));
			}
			else
			{
				$trace = \array_slice(\debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1);
			}
		}

		if ($additional == NULL)
		{
			$additional = array();
		}

		$this->_messages[] = array
		(
			'time'       => \time(),
			'level'      => $level,
			'body'       => $message,
			'trace'      => $trace,
			'file'       => isset($trace[0]['file']) ? $trace[0]['file'] : NULL,
			'line'       => isset($trace[0]['line']) ? $trace[0]['line'] : NULL,
			'class'      => isset($trace[0]['class']) ? $trace[0]['class'] : NULL,
			'function'   => isset($trace[0]['function']) ? $trace[0]['function'] : NULL,
			'additional' => $additional,
		);

		if (\Log::$write_on_add)
		{
			$this->write();
		}

		return $this;
	}


	public function write()
	{
		if (empty($this->_messages))
		{
			return;
		}

		$messages = $this->_messages;
		$this->_messages = array();

		foreach ($this->_writers as $writer)
		{
			if (empty($writer['levels']))
			{
				$writer['object']->write($messages);
			}
			else
			{
				$filtered = array();

				foreach ($messages as $message)
				{
					if (in_array($message['level'], $writer['levels']))
					{
						$filtered[] = $message;
					}
				}

				$writer['object']->write($filtered);
			}
		}
	}

}
