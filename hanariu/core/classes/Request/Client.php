<?php namespace Hanariu\Core;

abstract class Request_Client {

	protected $_cache;
	protected $_follow = FALSE;
	protected $_follow_headers = array('Authorization');
	protected $_strict_redirect = TRUE;
	protected $_header_callbacks = array(
		'Location'  => 'Client::on_header_location'
	);

	protected $_max_callback_depth = 5;
	protected $_callback_depth = 1;
	protected $_callback_params = array();

	public function __construct(array $params = array())
	{
		foreach ($params as $key => $value)
		{
			if (\method_exists($this, $key))
			{
				$this->$key($value);
			}
		}
	}

	public function execute(\Request $request)
	{
		if ($this->callback_depth() > $this->max_callback_depth())
			throw new Client\Recursion\Exception(
					"Could not execute request to :uri - too many recursions after :depth requests",
					array(
						':uri' => $request->uri(),
						':depth' => $this->callback_depth() - 1,
					));

		$orig_response = $response = \Response::factory();

		if (($cache = $this->cache()) instanceof \HTTP_Cache)
			return $cache->execute($this, $request, $response);

		$response = $this->execute_request($request, $response);

		foreach ($this->header_callbacks() as $header => $callback)
		{
			if ($response->headers($header))
			{
				$cb_result = \call_user_func($callback, $request, $response, $this);

				if ($cb_result instanceof \Request)
				{
					$this->assign_client_properties($cb_result->client());
					$cb_result->client()->callback_depth($this->callback_depth() + 1);
					$response = $cb_result->execute();
				}
				elseif ($cb_result instanceof \Response)
				{
					$response = $cb_result;
				}

				if ($response !== $orig_response)
					break;
			}
		}

		return $response;
	}

	abstract public function execute_request(\Request $request, \Response $response);

	public function cache(\HTTP_Cache $cache = NULL)
	{
		if ($cache === NULL)
			return $this->_cache;

		$this->_cache = $cache;
		return $this;
	}

	public function follow($follow = NULL)
	{
		if ($follow === NULL)
			return $this->_follow;

		$this->_follow = $follow;

		return $this;
	}

	public function follow_headers($follow_headers = NULL)
	{
		if ($follow_headers === NULL)
			return $this->_follow_headers;

		$this->_follow_headers = $follow_headers;

		return $this;
	}

	public function strict_redirect($strict_redirect = NULL)
	{
		if ($strict_redirect === NULL)
			return $this->_strict_redirect;

		$this->_strict_redirect = $strict_redirect;

		return $this;
	}

	public function header_callbacks($header_callbacks = NULL)
	{
		if ($header_callbacks === NULL)
			return $this->_header_callbacks;

		$this->_header_callbacks = $header_callbacks;

		return $this;
	}

	public function max_callback_depth($depth = NULL)
	{
		if ($depth === NULL)
			return $this->_max_callback_depth;

		$this->_max_callback_depth = $depth;

		return $this;
	}

	public function callback_depth($depth = NULL)
	{
		if ($depth === NULL)
			return $this->_callback_depth;

		$this->_callback_depth = $depth;

		return $this;
	}

	public function callback_params($param = NULL, $value = NULL)
	{
		if ($param === NULL)
			return $this->_callback_params;

		if (is_array($param))
		{
			$this->_callback_params = $param;
			return $this;
		}
		elseif ($value === NULL)
		{
			return \Arr::get($this->_callback_params, $param);
		}
		else
		{
			$this->_callback_params[$param] = $value;
			return $this;
		}

	}

	public function assign_client_properties(\Request_Client $client)
	{
		$client->cache($this->cache());
		$client->follow($this->follow());
		$client->follow_headers($this->follow_headers());
		$client->header_callbacks($this->header_callbacks());
		$client->max_callback_depth($this->max_callback_depth());
		$client->callback_params($this->callback_params());
	}


	public static function on_header_location(\Request $request, \Response $response, \Request_Client $client)
	{
		if ($client->follow() AND \in_array($response->status(), array(201, 301, 302, 303, 307)))
		{
			switch ($response->status())
			{
				default:
				case 301:
				case 307:
					$follow_method = $request->method();
					break;
				case 201:
				case 303:
					$follow_method = \Request::GET;
					break;
				case 302:
					if ($client->strict_redirect())
					{
						$follow_method = $request->method();
					}
					else
					{
						$follow_method = \Request::GET;
					}
					break;
			}

			$follow_request = \Request::factory($response->headers('Location'))
			                         ->method($follow_method)
			                         ->headers(\Arr::extract($request->headers(), $client->follow_headers()));

			if ($follow_method !== \Request::GET)
			{
				$follow_request->body($request->body());
			}

			return $follow_request;
		}

		return NULL;
	}

}
