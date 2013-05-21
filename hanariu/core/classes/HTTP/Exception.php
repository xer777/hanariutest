<?php namespace Hanariu\Core;

abstract class HTTP_Exception extends \Core_Exception {

	public static function factory($code, $message = NULL, array $variables = NULL, \Core_Exception $previous = NULL)
	{
		$class = '\\HTTP_Exception_E'.$code;
		
		return new $class($message, $variables, $previous);
	}

	protected $_code = 0;
	protected $_request;

	public function __construct($message = NULL, array $variables = NULL, \Core_Exception $previous = NULL)
	{
		parent::__construct($message, $variables, $this->_code, $previous);
	}

	public function request(\Request $request = NULL)
	{
		if ($request === NULL)
			return $this->_request;
		
		$this->_request = $request;

		return $this;
	}

	public function get_response()
	{
		return \Core_Exception::response($this);
	}
}