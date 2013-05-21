<?php namespace Hanariu\Core;

class HTTP_Exception_E302 extends \HTTP_Exception_Redirect {

	/**
	 * @var   integer    HTTP 302 Found
	 */
	protected $_code = 302;

}
