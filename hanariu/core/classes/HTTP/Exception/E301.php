<?php namespace Hanariu\Core;

class HTTP_Exception_E301 extends \HTTP_Exception_Redirect {

	/**
	 * @var   integer    HTTP 301 Moved Permanently
	 */
	protected $_code = 301;

}
