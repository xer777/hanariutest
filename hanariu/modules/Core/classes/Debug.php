<?php //namespace Core;

class Debug extends \Hanariu\Core\Debug {

	public static function vars()
	{
		if (func_num_args() === 0)
			return;

		$variables = \func_get_args();

		$output = array();
		foreach ($variables as $var)
		{
			$output[] = \Debug::_dump($var, 1024);
		}

		return '<pre class="debug">'.\implode("\n", $output).'</pre>';
	}


	public static function dump($value, $length = 128, $level_recursion = 10)
	{
		return \Debug::_dump($value, $length, $level_recursion);
	}

	protected static function _dump( & $var, $length = 128, $limit = 10, $level = 0)
	{
		if ($var === NULL)
		{
			return '<small>NULL</small>';
		}
		elseif (\is_bool($var))
		{
			return '<small>bool</small> '.($var ? 'TRUE' : 'FALSE');
		}
		elseif (\is_float($var))
		{
			return '<small>float</small> '.$var;
		}
		elseif (\is_resource($var))
		{
			if (($type = \get_resource_type($var)) === 'stream' AND $meta = \stream_get_meta_data($var))
			{
				$meta = \stream_get_meta_data($var);

				if (isset($meta['uri']))
				{
					$file = $meta['uri'];

					if (\function_exists('stream_is_local'))
					{
						// Only exists on PHP >= 5.2.4
						if (stream_is_local($file))
						{
							$file = \Debug::path($file);
						}
					}

					return '<small>resource</small><span>('.$type.')</span> '.\htmlspecialchars($file, ENT_NOQUOTES, \Hanariu::$charset);
				}
			}
			else
			{
				return '<small>resource</small><span>('.$type.')</span>';
			}
		}
		elseif (\is_string($var))
		{
			$var = \Utils::clean($var, \Hanariu::$charset);

			if (\Utils::strlen($var) > $length)
			{
				//$str = \htmlspecialchars(\Utils::substr($var, 0, $length), ENT_NOQUOTES, \Hanariu::$charset).'&nbsp;&hellip;';
				$str = \htmlspecialchars(substr($var, 0, $length), ENT_NOQUOTES, \Hanariu::$charset).'&nbsp;&hellip;';
			}
			else
			{
				$str = \htmlspecialchars($var, ENT_NOQUOTES, \Hanariu::$charset);
			}

			return '<small>string</small><span>('.\strlen($var).')</span> "'.$str.'"';
		}
		elseif (\is_array($var))
		{
			$output = array();
			$space = \str_repeat($s = '    ', $level);

			static $marker;

			if ($marker === NULL)
			{
				// Make a unique marker
				$marker = \uniqid("\x00");
			}

			if (empty($var))
			{
			}
			elseif (isset($var[$marker]))
			{
				$output[] = "(\n$space$s*RECURSION*\n$space)";
			}
			elseif ($level < $limit)
			{
				$output[] = "<span>(";

				$var[$marker] = TRUE;
				foreach ($var as $key => & $val)
				{
					if ($key === $marker) continue;
					if ( ! is_int($key))
					{
						$key = '"'.\htmlspecialchars($key, ENT_NOQUOTES, \Hanariu::$charset).'"';
					}

					$output[] = "$space$s$key => ".\Debug::_dump($val, $length, $limit, $level + 1);
				}
				unset($var[$marker]);

				$output[] = "$space)</span>";
			}
			else
			{
				$output[] = "(\n$space$s...\n$space)";
			}

			return '<small>array</small><span>('.\count($var).')</span> '.\implode("\n", $output);
		}
		elseif (\is_object($var))
		{
			$array = (array) $var;
			$output = array();
			$space = \str_repeat($s = '    ', $level);

			$hash = \spl_object_hash($var);

			// Objects that are being dumped
			static $objects = array();

			if (empty($var))
			{

			}
			elseif (isset($objects[$hash]))
			{
				$output[] = "{\n$space$s*RECURSION*\n$space}";
			}
			elseif ($level < $limit)
			{
				$output[] = "<code>{";

				$objects[$hash] = TRUE;
				foreach ($array as $key => & $val)
				{
					if ($key[0] === "\x00")
					{
						// Determine if the access is protected or protected
						$access = '<small>'.(($key[1] === '*') ? 'protected' : 'private').'</small>';

						// Remove the access level from the variable name
						$key = \substr($key, \strrpos($key, "\x00") + 1);
					}
					else
					{
						$access = '<small>public</small>';
					}

					$output[] = "$space$s$access $key => ".\Debug::_dump($val, $length, $limit, $level + 1);
				}
				unset($objects[$hash]);

				$output[] = "$space}</code>";
			}
			else
			{
				// Depth too great
				$output[] = "{\n$space$s...\n$space}";
			}

			return '<small>object</small> <span>'.\get_class($var).'('.\count($array).')</span> '.\implode("\n", $output);
		}
		else
		{
			return '<small>'.\gettype($var).'</small> '.\htmlspecialchars(\print_r($var, TRUE), ENT_NOQUOTES, \Hanariu::$charset);
		}
	}


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

	public static function source($file, $line_number, $padding = 5)
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
				$row = \htmlspecialchars($row, ENT_NOQUOTES, \Hanariu::$charset);
				$row = '<span class="number">'.\sprintf($format, $line).'</span> '.$row;

				if ($line === $line_number)
				{
					$row = '<span class="line highlight">'.$row.'</span>';
				}
				else
				{
					$row = '<span class="line">'.$row.'</span>';
				}

				$source .= $row;
			}
		}

		\fclose($file);

		return '<pre class="source"><code>'.$source.'</code></pre>';
	}

	public static function trace(array $trace = NULL)
	{
		if ($trace === NULL)
		{
			// Start a new trace
			$trace = \debug_backtrace();
		}

		$statements = array('include', 'include_once', 'require', 'require_once');

		$output = array();
		foreach ($trace as $step)
		{
			if ( ! isset($step['function']))
			{
				continue;
			}

			if (isset($step['file']) AND isset($step['line']))
			{
				$source = \Debug::source($step['file'], $step['line']);
			}

			if (isset($step['file']))
			{
				$file = $step['file'];

				if (isset($step['line']))
				{
					$line = $step['line'];
				}
			}

			$function = $step['function'];

			if (in_array($step['function'], $statements))
			{
				if (empty($step['args']))
				{
					$args = array();
				}
				else
				{
					$args = array($step['args'][0]);
				}
			}
			elseif (isset($step['args']))
			{
				if ( ! \function_exists($step['function']) OR \strpos($step['function'], '{closure}') !== FALSE)
				{
					$params = NULL;
				}
				else
				{
					if (isset($step['class']))
					{
						if (\method_exists($step['class'], $step['function']))
						{
							$reflection = new \ReflectionMethod($step['class'], $step['function']);
						}
						else
						{
							$reflection = new \ReflectionMethod($step['class'], '__call');
						}
					}
					else
					{
						$reflection = new \ReflectionFunction($step['function']);
					}

					$params = $reflection->getParameters();
				}

				$args = array();

				foreach ($step['args'] as $i => $arg)
				{
					if (isset($params[$i]))
					{
						$args[$params[$i]->name] = $arg;
					}
					else
					{
						$args[$i] = $arg;
					}
				}
			}

			if (isset($step['class']))
			{
				$function = $step['class'].$step['type'].$step['function'];
			}

			$output[] = array(
				'function' => $function,
				'args'     => isset($args)   ? $args : NULL,
				'file'     => isset($file)   ? $file : NULL,
				'line'     => isset($line)   ? $line : NULL,
				'source'   => isset($source) ? $source : NULL,
			);

			unset($function, $args, $file, $line, $source);
		}

		return $output;
	}

}
