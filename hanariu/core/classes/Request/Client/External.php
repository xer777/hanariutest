<?php namespace Hanariu\Core;

abstract class Request_Client_External extends Request_Client {

	public static $client = 'Curl';

	public static function factory(array $params = array(), $client = NULL)
	{
		if ($client === NULL)
		{
			$client = \Request_External::$client;
		}

		$client = new $client($params);

		if ( ! $client instanceof \Request_External)
		{
			throw new \Request_External('Selected client is not a Request_Client_External object.');
		}

		return $client;
	}

	protected $_options = array();

	public function execute_request(\Request $request, \Response $response)
	{
		if (\Hanariu::$profiling)
		{
			$benchmark = '"'.$request->uri().'"';

			if ($request !== \Request::$initial AND \Request::$current)
			{
				$benchmark .= ' « "'.\Request::$current->uri().'"';
			}
			$benchmark = \Profiler::start('Requests', $benchmark);
		}

		$previous = \Request::$current;
		\Request::$current = $request;
		if ($post = $request->post())
		{
			$request->body(\http_build_query($post, NULL, '&'))
				->headers('content-type', 'application/x-www-form-urlencoded');
		}

		if (\Hanariu::$expose)
		{
			$request->headers('user-agent', \Hanariu::version());
		}

		try
		{
			$response = $this->_send_message($request, $response);
		}
		catch (\Core_Exception $e)
		{
			\Request::$current = $previous;

			if (isset($benchmark))
			{
				\Profiler::delete($benchmark);
			}

			throw $e;
		}

		\Request::$current = $previous;

		if (isset($benchmark))
		{
			\Profiler::stop($benchmark);
		}

		return $response;
	}

	public function options($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_options;

		if (\is_array($key))
		{
			$this->_options = $key;
		}
		elseif ($value === NULL)
		{
			return \Arr::get($this->_options, $key);
		}
		else
		{
			$this->_options[$key] = $value;
		}

		return $this;
	}


	abstract protected function _send_message(\Request $request, \Response $response);

}
