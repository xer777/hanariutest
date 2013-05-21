<?php namespace Hanariu\Core;

class Log_StdErr extends \Log_Writer {

	public function write(array $messages)
	{
		foreach ($messages as $message)
		{
			\fwrite(STDERR, $this->format_message($message).PHP_EOL);
		}
	}

}
