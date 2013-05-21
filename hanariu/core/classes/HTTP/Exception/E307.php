<?php namespace Hanariu\Core;

class HTTP_Exception_E307 extends \HTTP_Exception_Redirect {

	/**
	 * @var   integer    HTTP 307 Temporary Redirect
	 */
	protected $_code = 307;

}
