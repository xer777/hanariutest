<?php namespace Hanariu\Core;

abstract class HTTP_Exception_Expected extends \HTTP_Exception {

	protected $_response;

	public function __construct($message = NULL, array $variables = NULL, \Core_Exception $previous = NULL)
	{
		parent::__construct($message, $variables, $previous);

		$this->_response = \Response::factory()
			->status($this->_code);
	}

	public function headers($key = NULL, $value = NULL)
	{
		$result = $this->_response->headers($key, $value);

		if ( ! $result instanceof \Response)
			return $result;

		return $this;
	}

	public function check()
	{
		return TRUE;
	}

	public function get_response()
	{
		$this->check();

		return $this->_response;
	}

}