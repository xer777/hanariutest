<?php namespace Hanariu\Core;

class Route {

	const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';
	const REGEX_SEGMENT = '[^/.,;?\n]++';
	const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';

	public static $default_protocol = 'http://';
	public static $localhosts = array(FALSE, '', 'local', 'localhost');
	public static $default_action = 'index';
	public static $cache = FALSE;
	protected static $_routes = array();

	public static function set($name, $uri = NULL, $regex = NULL)
	{
		return static::$_routes[$name] = new \Route($uri, $regex);
	}

	public static function get($name)
	{
		if ( ! isset(static::$_routes[$name]))
		{
			throw new \Core_Exception('The requested route does not exist: :route',
				array(':route' => $name));
		}

		return static::$_routes[$name];
	}

	public static function all()
	{
		return static::$_routes;
	}

	public static function name(Route $route)
	{
		return \array_search($route, static::$_routes);
	}

	public static function compile($uri, array $regex = NULL)
	{
		$expression = \preg_replace('#'.static::REGEX_ESCAPE.'#', '\\\\$0', $uri);

		if (\strpos($expression, '(') !== FALSE)
		{
			$expression = \str_replace(array('(', ')'), array('(?:', ')?'), $expression);
		}

		$expression = \str_replace(array('<', '>'), array('(?P<', '>'.static::REGEX_SEGMENT.')'), $expression);

		if ($regex)
		{
			$search = $replace = array();
			foreach ($regex as $key => $value)
			{
				$search[]  = "<$key>".static::REGEX_SEGMENT;
				$replace[] = "<$key>$value";
			}

			$expression = \str_replace($search, $replace, $expression);
		}

		return '#^'.$expression.'$#uD';
	}

	protected $_filters = array();
	protected $_uri = '';
	protected $_regex = array();
	protected $_defaults = array('action' => 'index', 'host' => FALSE);
	protected $_route_regex;


	public function __construct($uri = NULL, $regex = NULL)
	{
		if ($uri === NULL)
		{
			return;
		}

		if ( ! empty($uri))
		{
			$this->_uri = $uri;
		}

		if ( ! empty($regex))
		{
			$this->_regex = $regex;
		}

		$this->_route_regex = static::compile($uri, $regex);
	}

	public function defaults(array $defaults = NULL)
	{
		if ($defaults === NULL)
		{
			return $this->_defaults;
		}

		$this->_defaults = $defaults;

		return $this;
	}

	public function filter($callback)
	{
		if ( ! \is_callable($callback))
		{
			throw new \Core_Exception('Invalid Route::callback specified');
		}

		$this->_filters[] = $callback;

		return $this;
	}

	public function matches(\Request $request)
	{
		$uri = \trim($request->uri(), '/');

		if ( ! \preg_match($this->_route_regex, $uri, $matches))
			return FALSE;

		$params = array();
		foreach ($matches as $key => $value)
		{
			if (\is_int($key))
			{
				continue;
			}

			$params[$key] = $value;
		}

		foreach ($this->_defaults as $key => $value)
		{
			if ( ! isset($params[$key]) OR $params[$key] === '')
			{
				$params[$key] = $value;
			}
		}

		if ( ! empty($params['controller']))
		{
			$params['controller'] = \str_replace(' ', '_', \ucwords(\str_replace('_', ' ', $params['controller'])));
		}

		if ( ! empty($params['directory']))
		{
			$params['directory'] = \str_replace(' ', '_', \ucwords(\str_replace('_', ' ', $params['directory'])));
		}

		if ($this->_filters)
		{
			foreach ($this->_filters as $callback)
			{
				$return = \call_user_func($callback, $this, $params, $request);

				if ($return === FALSE)
				{
					return FALSE;
				}
				elseif (\is_array($return))
				{

					$params = $return;
				}
			}
		}

		return $params;
	}


	public function is_external()
	{
		return ! \in_array(\Arr::get($this->_defaults, 'host', FALSE), static::$localhosts);
	}

	public static function cache($save = FALSE, $append = FALSE)
	{
		if ($save === TRUE)
		{
			try
			{
				\Hanariu::cache('Route::cache()', static::$_routes);
			}
			catch (\Exception $e)
			{
				throw new \Core_Exception('One or more routes could not be cached (:message)', array(
						':message' => $e->getMessage(),
					), 0, $e);
			}
		}
		else
		{
			if ($routes = \Hanariu::cache('Route::cache()'))
			{
				if ($append)
				{
					// Append cached routes
					static::$_routes += $routes;
				}
				else
				{
					// Replace existing routes
					static::$_routes = $routes;
				}

				// Routes were cached
				return static::$cache = TRUE;
			}
			else
			{
				// Routes were not cached
				return static::$cache = FALSE;
			}
		}
	}

	public static function url($name, array $params = NULL, $protocol = NULL)
	{
		$route = static::get($name);
		if ($route->is_external())
			return static::get($name)->uri($params);
		else
			return \URL::site(static::get($name)->uri($params), $protocol);
	}

	public function uri(array $params = NULL)
	{
		$uri = $this->_uri;

		if (\strpos($uri, '<') === FALSE AND \strpos($uri, '(') === FALSE)
		{

			if ( ! $this->is_external())
				return $uri;

			if (\strpos($this->_defaults['host'], '://') === FALSE)
			{
				$params['host'] = static::$default_protocol.$this->_defaults['host'];
			}
			else
			{

				$params['host'] = $this->_defaults['host'];
			}

			return \rtrim($params['host'], '/').'/'.$uri;
		}

		$provided_optional = FALSE;

		while (\preg_match('#\([^()]++\)#', $uri, $match))
		{

			$search = $match[0];
			$replace = \substr($match[0], 1, -1);

			while (\preg_match('#'.static::REGEX_KEY.'#', $replace, $match))
			{
				list($key, $param) = $match;

				if (isset($params[$param]) AND $params[$param] !== \Arr::get($this->_defaults, $param))
				{
					$provided_optional = TRUE;
					$replace = \str_replace($key, $params[$param], $replace);
				}
				elseif ($provided_optional)
				{
					if (isset($this->_defaults[$param]))
					{
						$replace = \str_replace($key, $this->_defaults[$param], $replace);
					}
					else
					{
						throw new \Core_Exception('Required route parameter not passed: :param', array(
							':param' => $param,
						));
					}
				}
				else
				{
					$replace = '';
					break;
				}
			}

			$uri = \str_replace($search, $replace, $uri);
		}

		while (\preg_match('#'.static::REGEX_KEY.'#', $uri, $match))
		{
			list($key, $param) = $match;

			if ( ! isset($params[$param]))
			{
				if (isset($this->_defaults[$param]))
				{
					$params[$param] = $this->_defaults[$param];
				}
				else
				{

					throw new \Core_Exception('Required route parameter not passed: :param', array(
						':param' => $param,
					));
				}
			}

			$uri = \str_replace($key, $params[$param], $uri);
		}

		$uri = \preg_replace('#//+#', '/', \rtrim($uri, '/'));

		if ($this->is_external())
		{
			$host = $this->_defaults['host'];

			if (\strpos($host, '://') === FALSE)
			{
				$host = static::$default_protocol.$host;
			}

			$uri = \rtrim($host, '/').'/'.$uri;
		}

		return $uri;
	}

}
