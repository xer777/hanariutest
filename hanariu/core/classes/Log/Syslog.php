<?php namespace Hanariu\Core;

class Log_Syslog extends \Log_Writer {

	protected $_ident;

	public function __construct($ident = 'HanariuPHP', $facility = LOG_USER)
	{
		$this->_ident = $ident;
		\openlog($this->_ident, LOG_CONS, $facility);
	}

	public function write(array $messages)
	{
		foreach ($messages as $message)
		{
			\syslog($message['level'], $message['body']);

			if (isset($message['additional']['exception']))
			{
				\syslog(\Log_Writer::$strace_level, $message['additional']['exception']->getTraceAsString());
			}
		}
	}

	public function __destruct()
	{
		\closelog();
	}

} 
