<?php namespace Core;

class Validation_Exception extends \Core_Exception {

	public $array;

	public function __construct(\Core\Validation $array, $message = 'Failed to validate array', array $values = NULL, $code = 0, \Exception $previous = NULL)
	{
		$this->array = $array;

		parent::__construct($message, $values, $code, $previous);
	}

} 
