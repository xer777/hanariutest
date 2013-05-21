<?php namespace Hanariu\Core;

class Request_Client_Internal extends Request_Client {

	protected $_previous_environment;

	public function execute_request(\Request $request, \Response $response)
	{
		//$prefix = 'Controller\\';
		$prefix = 'Controller_';
		$directory = $request->directory();
		$controller = $request->controller();

		if ($directory)
		{
			$prefix .= \str_replace(array('\\', '/'), '_', \trim($directory, '/')).'\\';
		}

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
		$initial_request = ($request === \Request::$initial);

		try
		{
			if ( ! \class_exists($prefix.$controller))
			{
				throw \HTTP_Exception::factory(404,
					'The requested URL :uri  where prefix and controller are  :pc was not found on this server.',
					array(':uri' => $request->uri(),':pc' => $prefix.$controller)
				)->request($request);
			}

			$class = new \ReflectionClass($prefix.$controller);

			if ($class->isAbstract())
			{
				throw new \Core_Exception(
					'Cannot create instances of abstract :controller',
					array(':controller' => $prefix.$controller)
				);
			}

			$controller = $class->newInstance($request, $response);
			$response = $class->getMethod('execute')->invoke($controller);

			if ( ! $response instanceof \Response)
			{
				throw new \Core_Exception('Controller failed to return a Response');
			}
		}
		catch (\HTTP_Exception $e)
		{
			$response = $e->get_response();
		}
		catch (\Exception $e)
		{
			$response = \Core_Exception::_handler($e);
		}

		\Request::$current = $previous;

		if (isset($benchmark))
		{
			\Profiler::stop($benchmark);
		}

		return $response;
	}
}
