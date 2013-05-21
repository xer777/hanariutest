<?php namespace Core;

class Message
{

	public static function message($file, $path = NULL, $default = NULL)
	{
		static $messages;

		if ( ! isset($messages[$file]))
		{
			$messages[$file] = array();

			if ($files = \Hanariu::find_file('messages', $file))
			{
				foreach ($files as $f)
				{
					// Combine all the messages recursively
					$messages[$file] = \Arr::merge($messages[$file], \Hanariu::load($f));
				}
			}
		}

		if ($path === NULL)
		{
			// Return all of the messages
			return $messages[$file];
		}
		else
		{
			// Get a message using the path
			return \Arr::path($messages[$file], $path, $default);
		}
	}
}
