<?php namespace Hanariu\Core;

class Debug {

	public static function path($file)
	{
		if (\strpos($file, APPPATH) === 0)
		{
			$file = 'APPPATH'.DIRECTORY_SEPARATOR.\substr($file, \strlen(APPPATH));
		}
		elseif (\strpos($file, SYSPATH) === 0)
		{
			$file = 'SYSPATH'.DIRECTORY_SEPARATOR.\substr($file, \strlen(SYSPATH));
		}
		elseif (\strpos($file, MODPATH) === 0)
		{
			$file = 'MODPATH'.DIRECTORY_SEPARATOR.\substr($file, \strlen(MODPATH));
		}
		elseif (\strpos($file, DOCROOT) === 0)
		{
			$file = 'DOCROOT'.DIRECTORY_SEPARATOR.\substr($file, \strlen(DOCROOT));
		}

		return $file;
	}

	public static function source_plaintext($file, $line_number, $padding = 5)
	{
		if ( ! $file OR ! \is_readable($file))
		{

			return FALSE;
		}

		$file = \fopen($file, 'r');
		$line = 0;
		$range = array('start' => $line_number - $padding, 'end' => $line_number + $padding);
		$format = '% '.\strlen($range['end']).'d';
		$source = '';
		while (($row = \fgets($file)) !== FALSE)
		{
			// Increment the line number
			if (++$line > $range['end'])
				break;

			if ($line >= $range['start'])
			{
				if ($line === $line_number)
				{
					$row = 'line #'.\sprintf($format, $line).' ERROR: '.$row;
				}
				else
				{
					$row = 'line #'.\sprintf($format, $line).' '.$row;
				}

				$source .= $row;
			}
		}

		\fclose($file);

		return $source;
	}
}
