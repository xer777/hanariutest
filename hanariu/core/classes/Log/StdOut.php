<?php namespace Hanariu\Core;

class Log_StdOut extends \Log_Writer {

	public function write(array $messages)
	{
		foreach ($messages as $message)
		{
			\fwrite(STDOUT, $this->format_message($message).PHP_EOL);
		}
	}

}
