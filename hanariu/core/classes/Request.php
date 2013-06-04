<?php namespace Hanariu\Core;

class Request implements HTTP_Request {

	public static $user_agent = '';
	public static $client_ip = '0.0.0.0';
	public static $trusted_proxies = array('127.0.0.1', 'localhost', 'localhost.localdomain');
	public static $initial;
	public static $current;

	public static function factory($uri = TRUE, $client_params = array(), $allow_external = TRUE, $injected_routes = array())
	{
		if ( ! static::$initial)
		{
			if (isset($_SERVER['SERVER_PROTOCOL']))
			{
				$protocol = $_SERVER['SERVER_PROTOCOL'];
			}
			else
			{
				$protocol = HTTP::$protocol;
			}

			if (isset($_SERVER['REQUEST_METHOD']))
			{
				$method = $_SERVER['REQUEST_METHOD'];
			}
			else
			{
				$method = \HTTP_Request::GET;
			}

			if ( ! empty($_SERVER['HTTPS']) AND \filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN))
			{
				$secure = TRUE;
			}

			if (isset($_SERVER['HTTP_REFERER']))
			{
				$referrer = $_SERVER['HTTP_REFERER'];
			}

			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				static::$user_agent = $_SERVER['HTTP_USER_AGENT'];
			}

			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
			{
				$requested_with = $_SERVER['HTTP_X_REQUESTED_WITH'];
			}

			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
			    AND isset($_SERVER['REMOTE_ADDR'])
			    AND \in_array($_SERVER['REMOTE_ADDR'], static::$trusted_proxies))
			{

				$client_ips = \explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				static::$client_ip = \array_shift($client_ips);

				unset($client_ips);
			}
			elseif (isset($_SERVER['HTTP_CLIENT_IP'])
			        AND isset($_SERVER['REMOTE_ADDR'])
			        AND \in_array($_SERVER['REMOTE_ADDR'], static::$trusted_proxies))
			{
				$client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);

				static::$client_ip = \array_shift($client_ips);

				unset($client_ips);
			}
			elseif (isset($_SERVER['REMOTE_ADDR']))
			{
				static::$client_ip = $_SERVER['REMOTE_ADDR'];
			}

			if ($method !== \HTTP_Request::GET)
			{
				$body = \file_get_contents('php://input');
			}

			if ($uri === TRUE)
			{
				$uri = static::detect_uri();
			}

			$cookies = array();

			if (($cookie_keys = \array_keys($_COOKIE)))
			{
				foreach ($cookie_keys as $key)
				{
					$cookies[$key] = \Cookie::get($key);
				}
			}

			static::$initial = $request = new \Request($uri, $client_params, $allow_external, $injected_routes);

			$request->protocol($protocol)
				->query($_GET)
				->post($_POST);

			if (isset($secure))
			{
				$request->secure($secure);
			}

			if (isset($method))
			{
				$request->method($method);
			}

			if (isset($referrer))
			{
				$request->referrer($referrer);
			}

			if (isset($requested_with))
			{
				$request->requested_with($requested_with);
			}

			if (isset($body))
			{
				$request->body($body);
			}

			if (isset($cookies))
			{
				$request->cookie($cookies);
			}
		}
		else
		{
			$request = new \Request($uri, $client_params, $allow_external, $injected_routes);
		}

		return $request;
	}

	public static function detect_uri()
	{
		if ( ! empty($_SERVER['PATH_INFO']))
		{
			$uri = $_SERVER['PATH_INFO'];
		}
		else
		{

			if (isset($_SERVER['REQUEST_URI']))
			{

				$uri = $_SERVER['REQUEST_URI'];

				if ($request_uri = \parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
				{
					$uri = $request_uri;
				}

				$uri = \rawurldecode($uri);
			}
			elseif (isset($_SERVER['PHP_SELF']))
			{
				$uri = $_SERVER['PHP_SELF'];
			}
			elseif (isset($_SERVER['REDIRECT_URL']))
			{
				$uri = $_SERVER['REDIRECT_URL'];
			}
			else
			{
				throw new \Core_Exception('Unable to detect the URI using PATH_INFO, REQUEST_URI, PHP_SELF or REDIRECT_URL');
			}

			$base_url = \parse_url(\Hanariu::$base_url, PHP_URL_PATH);

			if (\strpos($uri, $base_url) === 0)
			{
				$uri = (string) \substr($uri, \strlen($base_url));
			}

			if (\Hanariu::$index_file AND \strpos($uri, \Hanariu::$index_file) === 0)
			{
				$uri = (string) \substr($uri, \strlen(\Hanariu::$index_file));
			}
		}

		return $uri;
	}

	public static function current()
	{
		return static::$current;
	}

	public static function initial()
	{
		return static::$initial;
	}

	public static function user_agent($value)
	{
		return \Utils::user_agent(static::$user_agent, $value);
	}

	public static function accept_type($type = NULL)
	{
		static $accepts;

		if ($accepts === NULL)
		{
			$accepts = static::_parse_accept($_SERVER['HTTP_ACCEPT'], array('*/*' => 1.0));
		}

		if (isset($type))
		{
			return isset($accepts[$type]) ? $accepts[$type] : $accepts['*/*'];
		}

		return $accepts;
	}

	public static function accept_lang($lang = NULL)
	{
		static $accepts;

		if ($accepts === NULL)
		{
			$accepts = static::_parse_accept($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		}

		if (isset($lang))
		{
			return isset($accepts[$lang]) ? $accepts[$lang] : FALSE;
		}

		return $accepts;
	}

	public static function accept_encoding($type = NULL)
	{
		static $accepts;

		if ($accepts === NULL)
		{
			$accepts = static::_parse_accept($_SERVER['HTTP_ACCEPT_ENCODING']);
		}

		if (isset($type))
		{
			return isset($accepts[$type]) ? $accepts[$type] : FALSE;
		}

		return $accepts;
	}


	public static function post_max_size_exceeded()
	{
		if (static::$initial->method() !== \HTTP_Request::POST)
			return FALSE;
		$max_bytes = \Utils::bytes(\ini_get('post_max_size'));
		return (\Arr::get($_SERVER, 'CONTENT_LENGTH') > $max_bytes);
	}

	public static function process(Request $request, $routes = NULL)
	{
		$routes = (empty($routes)) ? \Route::all() : $routes;
		$params = NULL;

		foreach ($routes as $name => $route)
		{
			if ($params = $route->matches($request))
			{
				return array(
					'params' => $params,
					'route' => $route,
				);
			}
		}

		return NULL;
	}

	protected static function _parse_accept( & $header, array $accepts = NULL)
	{
		if ( ! empty($header))
		{
			$types = \explode(',', $header);

			foreach ($types as $type)
			{
				$parts = \explode(';', $type);
				$type = \trim(\array_shift($parts));
				$quality = 1.0;

				foreach ($parts as $part)
				{
					if (strpos($part, '=') === FALSE)
						continue;

					list ($key, $value) = \explode('=', \trim($part));

					if ($key === 'q')
					{
						$quality = (float) \trim($value);
					}
				}

				$accepts[$type] = $quality;
			}
		}

		$accepts = (array) $accepts;
		\arsort($accepts);

		return $accepts;
	}

	protected $_requested_with;
	protected $_method = 'GET';
	protected $_protocol;
	protected $_secure = FALSE;
	protected $_referrer;
	protected $_route;
	protected $_routes;
	protected $_header;
	protected $_body;
	protected $_lang = '';
	protected $_app;
	protected $_directory = '';
	protected $_controller;
	protected $_action;
	protected $_uri;
	protected $_external = FALSE;
	protected $_params = array();
	protected $_get = array();
	protected $_post = array();
	protected $_cookies = array();
	protected $_client;


	public function __construct($uri, $client_params = array(), $allow_external = TRUE, $injected_routes = array())
	{
		$client_params = \is_array($client_params) ? $client_params : array();
		$this->_header = new \HTTP_Header(array());
		$this->_routes = $injected_routes;
		$split_uri = \explode('?', $uri);
		$uri = \array_shift($split_uri);

		if (static::$initial !== NULL)
		{
			if ($split_uri)
			{
				\parse_str($split_uri[0], $this->_get);
			}
		}

		if ( ! $allow_external OR \strpos($uri, '://') === FALSE)
		{
			$this->_uri = \trim($uri, '/');
			$this->_client = new \Request_Client_Internal($client_params);
		}
		else
		{
			$this->_route = new \Route($uri);
			$this->_uri = $uri;

			if (\strpos($uri, 'https://') === 0)
			{
				$this->secure(TRUE);
			}

			$this->_external = TRUE;
			$this->_client = \Request_Client_External::factory($client_params);
		}
	}


	public function __toString()
	{
		return $this->render();
	}

	public function uri($uri = NULL)
	{
		if ($uri === NULL)
		{
			return empty($this->_uri) ? '/' : $this->_uri;
		}

		$this->_uri = $uri;

		return $this;
	}


	public function url($protocol = NULL)
	{
		return \URL::site($this->uri(), $protocol);
	}


	public function param($key = NULL, $default = NULL)
	{
		if ($key === NULL)
		{
			return $this->_params;
		}

		return isset($this->_params[$key]) ? $this->_params[$key] : $default;
	}


	public function referrer($referrer = NULL)
	{
		if ($referrer === NULL)
		{
			return $this->_referrer;
		}

		$this->_referrer = (string) $referrer;
		return $this;
	}

	public function route(Route $route = NULL)
	{
		if ($route === NULL)
		{
			return $this->_route;
		}

		$this->_route = $route;
		return $this;
	}

	public function app($app = NULL)
	{
		if ($app === NULL)
		{
			return $this->_app;
		}

		$this->_app = (string) $app;
		return $this;
	}

	public function lang($lang = NULL)
	{
		if ($lang === NULL)
		{
			return $this->_lang;
		}

		$this->_lang = (string) $lang;
		return $this;
	}
	public function directory($directory = NULL)
	{
		if ($directory === NULL)
		{
			return $this->_directory;
		}

		$this->_directory = (string) $directory;
		return $this;
	}

	public function controller($controller = NULL)
	{
		if ($controller === NULL)
		{
			return $this->_controller;
		}

		$this->_controller = (string) $controller;
		return $this;
	}

	/**
	 * Sets and gets the action for the controller.
	 *
	 * @param   string   $action  Action to execute the controller from
	 * @return  mixed
	 */
	public function action($action = NULL)
	{
		if ($action === NULL)
		{
			// Act as a getter
			return $this->_action;
		}

		$this->_action = (string) $action;
		return $this;
	}


	public function client(Request\Client $client = NULL)
	{
		if ($client === NULL)
			return $this->_client;
		else
		{
			$this->_client = $client;
			return $this;
		}
	}


	public function requested_with($requested_with = NULL)
	{
		if ($requested_with === NULL)
		{
			return $this->_requested_with;
		}

		$this->_requested_with = \strtolower($requested_with);
		return $this;
	}


	public function execute()
	{
		if ( ! $this->_external)
		{
			$processed = static::process($this, $this->_routes);

			if ($processed)
			{
				$this->_route = $processed['route'];
				$params = $processed['params'];
				$this->_external = $this->_route->is_external();

				if (isset($params['lang']))
				{
					$this->_lang = $params['lang'];
				}

				if (isset($params['app']))
				{
					$this->_app = $params['app'];
				}

				if (isset($params['directory']))
				{
					$this->_directory = $params['directory'];
				}

				$this->_controller = $params['controller'];
				$this->_action = (isset($params['action']))
					? $params['action']
					: \Route::$default_action;

				unset($params['controller'], $params['action'], $params['directory'], $params['app'],$params['controller']);
				$this->_params = $params;
			}
		}

		if ( ! $this->_route instanceof \Route)
		{
			return \HTTP_Exception::factory(404, 'Unable to find a route to match the URI: :uri', array(
				':uri' => $this->_uri,
			))->request($this)
				->get_response();
		}

		if ( ! $this->_client instanceof \Hanariu\Core\Request_Client)
		{
			throw new \Core_Exception('Unable to execute :uri without a Request_Client', array(
				':uri' => $this->_uri,
			));
		}

		return $this->_client->execute($this);
	}

	public function is_initial()
	{
		return ($this === static::$initial);
	}

	public function is_external()
	{
		return $this->_external;
	}

	public function is_ajax()
	{
		return ($this->requested_with() === 'xmlhttprequest');
	}

	public function method($method = NULL)
	{
		if ($method === NULL)
		{
			return $this->_method;
		}

		$this->_method = \strtoupper($method);
		return $this;
	}

	public function protocol($protocol = NULL)
	{
		if ($protocol === NULL)
		{
			if ($this->_protocol)
				return $this->_protocol;
			else
				return $this->_protocol = \HTTP::$protocol;
		}

		$this->_protocol = \strtoupper($protocol);
		return $this;
	}

	public function secure($secure = NULL)
	{
		if ($secure === NULL)
			return $this->_secure;

		$this->_secure = (bool) $secure;
		return $this;
	}

	public function headers($key = NULL, $value = NULL)
	{
		if ($key instanceof \HTTP_Header)
		{
			$this->_header = $key;
			return $this;
		}

		if (is_array($key))
		{
			$this->_header->exchangeArray($key);
			return $this;
		}

		if ($this->_header->count() === 0 AND $this->is_initial())
		{
			$this->_header = \HTTP::request_headers();
		}

		if ($key === NULL)
		{
			return $this->_header;
		}
		elseif ($value === NULL)
		{
			return ($this->_header->offsetExists($key)) ? $this->_header->offsetGet($key) : NULL;
		}

		$this->_header[$key] = $value;

		return $this;
	}

	public function cookie($key = NULL, $value = NULL)
	{
		if (\is_array($key))
		{
			$this->_cookies = $key;
			return $this;
		}
		elseif ($key === NULL)
		{
			return $this->_cookies;
		}
		elseif ($value === NULL)
		{
			return isset($this->_cookies[$key]) ? $this->_cookies[$key] : NULL;
		}

		$this->_cookies[$key] = (string) $value;
		return $this;
	}

	public function body($content = NULL)
	{
		if ($content === NULL)
		{
			return $this->_body;
		}

		$this->_body = $content;
		return $this;
	}

	public function content_length()
	{
		return \strlen($this->body());
	}

	public function render()
	{
		if ( ! $post = $this->post())
		{
			$body = $this->body();
		}
		else
		{
			$this->headers('content-type', 'application/x-www-form-urlencoded');
			$body = \http_build_query($post, NULL, '&');
		}

		$this->headers('content-length', (string) $this->content_length());
		if (\Hanariu::$expose)
		{
			$this->headers('user-agent', \Hanariu::version());
		}

		if ($this->_cookies)
		{
			$cookie_string = array();

			foreach ($this->_cookies as $key => $value)
			{
				$cookie_string[] = $key.'='.$value;
			}

			$this->_header['cookie'] = \implode('; ', $cookie_string);
		}

		$output = $this->method().' '.$this->uri().' '.$this->protocol()."\r\n";
		$output .= (string) $this->_header;
		$output .= $body;

		return $output;
	}

	public function query($key = NULL, $value = NULL)
	{
		if (is_array($key))
		{
			$this->_get = $key;
			return $this;
		}

		if ($key === NULL)
		{
			return $this->_get;
		}
		elseif ($value === NULL)
		{
			return \Arr::path($this->_get, $key);
		}

		$this->_get[$key] = $value;
		return $this;
	}

	public function post($key = NULL, $value = NULL)
	{
		if (\is_array($key))
		{
			$this->_post = $key;

			return $this;
		}

		if ($key === NULL)
		{
			return $this->_post;
		}
		elseif ($value === NULL)
		{
			return \Arr::path($this->_post, $key);
		}

		$this->_post[$key] = $value;
		return $this;
	}

}
