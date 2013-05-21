<?php

namespace Hanariu\Core;

class Autoloader
{

	protected static $classes = array();
	protected static $namespaces = array();
	protected static $psr_namespaces = array();
	protected static $core_namespaces = array('Hanariu\\Core');
	protected static $default_path = null;
	protected static $auto_initialize = null;

	public static function add_namespace($namespace, $path, $psr = false)
	{
		static::$namespaces[$namespace] = $path;
		if ($psr)
		{
			static::$psr_namespaces[$namespace] = $path;
		}
	}

	public static function add_namespaces(array $namespaces, $prepend = false)
	{
		if ( ! $prepend)
		{
			static::$namespaces = array_merge(static::$namespaces, $namespaces);
		}
		else
		{
			static::$namespaces = $namespaces + static::$namespaces;
		}
	}

	public static function namespace_path($namespace)
	{
		if ( ! array_key_exists($namespace, static::$namespaces))
		{
			return false;
		}

		return static::$namespaces[$namespace];
	}

	public static function add_class($class, $path)
	{
		static::$classes[$class] = $path;
	}

	public static function add_classes($classes)
	{
		foreach ($classes as $class => $path)
		{
			static::$classes[$class] = $path;
		}
	}

	public static function alias_to_namespace($class, $namespace = '')
	{
		empty($namespace) or $namespace = rtrim($namespace, '\\').'\\';
		$parts = explode('\\', $class);
		$root_class = $namespace.array_pop($parts);
		class_alias($class, $root_class);
	}

	public static function register()
	{
		spl_autoload_register('Autoloader::load', true, true);
	}

	protected static function find_core_class($class)
	{
		foreach (static::$core_namespaces as $ns)
		{
			if (array_key_exists($ns_class = $ns.'\\'.$class, static::$classes))
			{
				return $ns_class;
			}
		}

		return false;
	}

	public static function add_core_namespace($namespace, $prefix = true)
	{
		if ($prefix)
		{
			array_unshift(static::$core_namespaces, $namespace);
		}
		else
		{
			array_push(static::$core_namespaces, $namespace);
		}
	}

	public static function load($class)
	{
		// deal with funny is_callable('static::classname') side-effect
		if (strpos($class, 'static::') === 0)
		{
			// is called from within the class, so it's already loaded
			return true;
		}

		$loaded = false;
		$class = ltrim($class, '\\');
		$namespaced = ($pos = strripos($class, '\\')) !== false;

		if (empty(static::$auto_initialize))
		{
			static::$auto_initialize = $class;
		}

		if (isset(static::$classes[$class]))
		{
			include str_replace('/', DIRECTORY_SEPARATOR, static::$classes[$class]);
			static::init_class($class);
			$loaded = true;
		}
		elseif ($full_class = static::find_core_class($class))
		{
			if ( ! class_exists($full_class, false) and ! interface_exists($full_class, false))
			{
				include static::prep_path(static::$classes[$full_class]);
			}
			class_alias($full_class, $class);
			static::init_class($class);
			$loaded = true;
		}
		else
		{
			$full_ns = substr($class, 0, $pos);

			if ($full_ns)
			{
				foreach (static::$namespaces as $ns => $path)
				{
					$ns = ltrim($ns, '\\');
					if (stripos($full_ns, $ns) === 0)
					{
						$path .= static::class_to_path(
							substr($class, strlen($ns) + 1),
							array_key_exists($ns, static::$psr_namespaces)
						);
						if (is_file($path))
						{
							require $path;
							static::init_class($class);
							$loaded = true;
							break;
						}
					}
				}
			}

			if ( ! $loaded)
			{
				$path = APPPATH.'classes/'.static::class_to_path($class);

				if (file_exists($path))
				//if ($path = \Hanariu::find_file('classes', $class))
				{
					include $path;
					static::init_class($class);
					$loaded = true;
				}
			}
		}

		// Prevent failed load from keeping other classes from initializing
		if (static::$auto_initialize == $class)
		{
			static::$auto_initialize = null;
		}

		return $loaded;
	}

	public static function _reset()
	{
		static::$auto_initialize = null;
	}

	protected static function class_to_path($class, $psr = false)
	{
		$file  = '';
		if ($last_ns_pos = strripos($class, '\\'))
		{
			$namespace = substr($class, 0, $last_ns_pos);
			$class = substr($class, $last_ns_pos + 1);
			$file = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
		}
		$file .= str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

		if ( ! $psr)
		{
			$file = strtolower($file);
		}

		return $file;
	}

	protected static function prep_path($path)
	{
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	}

	protected static function init_class($class)
	{
		if (static::$auto_initialize === $class)
		{
			static::$auto_initialize = null;
			if (method_exists($class, '_init') and is_callable($class.'::_init'))
			{
				call_user_func($class.'::_init');
			}
		}
	}
}
