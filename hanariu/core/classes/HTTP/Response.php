<?php namespace Hanariu\Core;

interface HTTP_Response extends \HTTP_Message {

	public function status($code = NULL);

}
