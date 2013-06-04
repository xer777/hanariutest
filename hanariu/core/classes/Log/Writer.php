<?php namespace Hanariu\Core;

abstract class Log_Writer {

	public static $timestamp;

	public static $timezone;

	protected $_log_levels = array(
		LOG_EMERG   => 'EMERGENCY',
		LOG_ALERT   => 'ALERT',
		LOG_CRIT    => 'CRITICAL',
		LOG_ERR     => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE  => 'NOTICE',
		LOG_INFO    => 'INFO',
		LOG_DEBUG   => 'DEBUG',
	);

	public static $strace_level = LOG_DEBUG;
	abstract public function write(array $messages);

	final public function __toString()
	{
		return \spl_object_hash($this);
	}


	public function format_message(array $message, $format = "time --- level: body in file:line")
	{
		$message['time'] = \Utils::formatted_time('@'.$message['time'], static::$timestamp, static::$timezone, TRUE);
		$message['level'] = $this->_log_levels[$message['level']];

		$string = strtr($format, $message);

		if (isset($message['additional']['exception']))
		{
			// Re-use as much as possible, just resetting the body to the trace
			$message['body'] = $message['additional']['exception']->getTraceAsString();
			$message['level'] = $this->_log_levels[static::$strace_level];

			$string .= PHP_EOL.\strtr($format, $message);
		}

		return $string;
	}

}
