<?php namespace Hanariu\Core;

class HTTP_Exception_E304 extends \HTTP_Exception_Expected {

	/**
	 * @var   integer    HTTP 304 Not Modified
	 */
	protected $_code = 304;
	
}
