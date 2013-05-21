<?php namespace Hanariu\Core;

interface HTTP_Request extends HTTP_Message {

	// HTTP Methods
	const GET       = 'GET';
	const POST      = 'POST';
	const PUT       = 'PUT';
	const DELETE    = 'DELETE';
	const HEAD      = 'HEAD';
	const OPTIONS   = 'OPTIONS';
	const TRACE     = 'TRACE';
	const CONNECT   = 'CONNECT';

	public function method($method = NULL);
	public function uri();
	public function query($key = NULL, $value = NULL);
	public function post($key = NULL, $value = NULL);

}
