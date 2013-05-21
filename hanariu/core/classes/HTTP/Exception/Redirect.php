<?php namespace Hanariu\Core;

abstract class HTTP_Exception_Redirect extends \HTTP_Exception_Expected {

	public function location($uri = NULL)
	{
		if ($uri === NULL)
			return $this->headers('Location');
		
		if (\strpos($uri, '://') === FALSE)
		{
			$uri = \URL::site($uri, TRUE, ! empty(\Hanariu::$index_file));
		}

		$this->headers('Location', $uri);

		return $this;
	}

	public function check()
	{
		if ($this->headers('location') === NULL)
			throw new \Core_Exception('A \'location\' must be specified for a redirect');

		return TRUE;
	}

}