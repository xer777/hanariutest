<?php namespace Hanariu\Core;

class HTTP_Exception_E405 extends \HTTP_Exception_Expected {


	protected $_code = 405;


	public function allowed($methods)
	{
		if (\is_array($methods))
		{
			$methods = \implode(',', $methods);
		}

		$this->headers('allow', $methods);

		return $this;
	}


	public function check()
	{
		if ($location = $this->headers('allow') === NULL)
			throw new \Core_Exception('A list of allowed methods must be specified');

		return TRUE;
	}

}
